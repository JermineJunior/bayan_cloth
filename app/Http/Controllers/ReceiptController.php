<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\ReceiptConfirmation;
use App\Models\Stock;
use App\Models\StockDetail;
use App\Models\Transform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    private const START_DATE = '2026-11-06';

    public function index(Request $request): View
    {
        $this->authorizeReceiptConfirmation();

        $status = in_array($request->input('status', 'pending'), ['pending', 'confirmed', 'all'], true)
            ? $request->input('status', 'pending')
            : 'pending';
        $type = in_array($request->input('type', 'all'), ['all', 'purchase', 'transform'], true)
            ? $request->input('type', 'all')
            : 'all';

        $confirmations = ReceiptConfirmation::with('user')
            ->whereIn('document_type', [ReceiptConfirmation::TYPE_PURCHASE, ReceiptConfirmation::TYPE_TRANSFORM])
            ->get()
            ->keyBy(fn (ReceiptConfirmation $confirmation) => $confirmation->document_type . '_' . $confirmation->document_id);

        $documents = collect();

        if (in_array($type, ['all', 'purchase'], true)) {
            $purchases = PurchaseInvoice::with(['supplier', 'user', 'branch'])
                ->where('branch_id', session('branch_id'))
                ->whereDate('created_at', '>=', self::START_DATE)
                ->get();

            foreach ($purchases as $purchase) {
                $documents->push([
                    'type' => ReceiptConfirmation::TYPE_PURCHASE,
                    'type_label' => 'مشتريات',
                    'id' => $purchase->id,
                    'number' => $purchase->display_number,
                    'date' => $purchase->date,
                    'source' => $purchase->supplier?->tree4_name ?? '-',
                    'created_by' => $purchase->user?->name ?? '-',
                    'created_at' => $purchase->created_at,
                    'confirmation' => $confirmations->get(ReceiptConfirmation::TYPE_PURCHASE . '_' . $purchase->id),
                ]);
            }
        }

        if (in_array($type, ['all', 'transform'], true)) {
            $transforms = Transform::with(['fromStock', 'toStock'])
                ->whereIn('stock_to', Stock::pluck('id'))
                ->whereDate('created_at', '>=', self::START_DATE)
                ->get();

            $transformUsers = StockDetail::with('user')
                ->where('type', 6)
                ->whereIn('transform_id', $transforms->pluck('id'))
                ->get()
                ->groupBy('transform_id')
                ->map(fn ($details) => $details->first()?->user?->name);

            foreach ($transforms as $transform) {
                $documents->push([
                    'type' => ReceiptConfirmation::TYPE_TRANSFORM,
                    'type_label' => 'تحويل',
                    'id' => $transform->id,
                    'number' => $transform->id,
                    'date' => $transform->created_at?->format('Y-m-d'),
                    'source' => ($transform->fromStock?->name ?? '-') . ' -> ' . ($transform->toStock?->name ?? '-'),
                    'created_by' => $transformUsers->get($transform->id) ?? '-',
                    'created_at' => $transform->created_at,
                    'confirmation' => $confirmations->get(ReceiptConfirmation::TYPE_TRANSFORM . '_' . $transform->id),
                ]);
            }
        }

        $documents = $documents
            ->filter(fn (array $document) => match ($status) {
                'pending' => $document['confirmation'] === null,
                'confirmed' => $document['confirmation'] !== null,
                default => true,
            })
            ->sortByDesc('created_at')
            ->values();

        return view('receipts.index', compact('documents', 'status', 'type'));
    }

    public function show(string $type, int $id): View
    {
        $this->authorizeReceiptConfirmation();

        $document = $this->findDocument($type, $id);

        if ($type === ReceiptConfirmation::TYPE_PURCHASE) {
            $document->load(['supplier', 'stock', 'user', 'details.product', 'details.unit']);
        } else {
            $document->load(['fromStock', 'toStock', 'transformDetails.product', 'transformDetails.unit']);
        }

        $confirmation = ReceiptConfirmation::with('user')
            ->where('document_type', $type)
            ->where('document_id', $document->id)
            ->first();

        return view('receipts.show', compact('document', 'type', 'confirmation'));
    }

    public function confirm(string $type, int $id): RedirectResponse
    {
        $this->authorizeReceiptConfirmation();

        $document = $this->findDocument($type, $id);

        $confirmation = ReceiptConfirmation::firstOrCreate(
            [
                'document_type' => $type,
                'document_id' => $document->id,
            ],
            [
                'user_id' => Auth::id(),
                'branch_id' => session('branch_id'),
            ],
        );

        abort_unless($confirmation->wasRecentlyCreated, 409, 'تم تأكيد استلام هذا المستند بالفعل');

        return back()->with('success_created', 'تم تأكيد الاستلام بنجاح');
    }

    public function unconfirm(string $type, int $id): RedirectResponse
    {
        abort_unless(Auth::user()?->can('إلغاء تأكيد الاستلام'), 403);

        $document = $this->findDocument($type, $id);

        ReceiptConfirmation::where('document_type', $type)
            ->where('document_id', $document->id)
            ->firstOrFail()
            ->delete();

        return back()->with('success_deleted', 'تم إلغاء تأكيد الاستلام بنجاح');
    }

    private function findDocument(string $type, int $id): PurchaseInvoice|Transform
    {
        abort_unless(in_array($type, [ReceiptConfirmation::TYPE_PURCHASE, ReceiptConfirmation::TYPE_TRANSFORM], true), 404);

        if ($type === ReceiptConfirmation::TYPE_PURCHASE) {
            return PurchaseInvoice::where('branch_id', session('branch_id'))
                ->whereDate('created_at', '>=', self::START_DATE)
                ->findOrFail($id);
        }

        return Transform::whereIn('stock_to', Stock::pluck('id'))
            ->whereDate('created_at', '>=', self::START_DATE)
            ->findOrFail($id);
    }

    private function authorizeReceiptConfirmation(): void
    {
        abort_unless(Auth::user()?->can('تأكيد استلام المخزون'), 403);
    }
}
