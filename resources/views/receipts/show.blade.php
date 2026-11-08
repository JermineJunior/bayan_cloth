@extends('layouts.app')

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto">تأكيد الاستلام</h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ عرض المستند</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    @if ($errors->any())
                        <div class="alert alert-outline-danger" role="alert">
                            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                <span aria-hidden="true">&times;</span></button>
                            @foreach ($errors->all() as $error)
                                <strong>خطأ!</strong> {{ $error }}
                            @endforeach
                        </div>
                    @endif

                    @if (session()->has('success_created'))
                        <div class="alert alert-success" role="alert">{{ session('success_created') }}</div>
                    @endif

                    @if (session()->has('success_deleted'))
                        <div class="alert alert-success" role="alert">{{ session('success_deleted') }}</div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">
                                {{ $type === 'purchase' ? 'فاتورة مشتريات' : 'تحويل بين المخازن' }}
                                #{{ $type === 'purchase' ? $document->display_number : $document->id }}
                            </h4>
                            <p class="mb-0 text-muted">
                                {{ $type === 'purchase' ? $document->date : $document->created_at?->format('Y-m-d') }}
                            </p>
                        </div>
                        <div>
                            @if ($confirmation)
                                <span class="badge badge-success">مؤكد</span>
                            @else
                                <span class="badge badge-warning">بانتظار التأكيد</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        @if ($type === 'purchase')
                            <div class="col-md-4">
                                <strong>المورد:</strong>
                                {{ $document->supplier?->tree4_name ?? '-' }}
                            </div>
                            <div class="col-md-4">
                                <strong>المخزن:</strong>
                                {{ $document->stock?->name ?? '-' }}
                            </div>
                            <div class="col-md-4">
                                <strong>أنشأه:</strong>
                                {{ $document->user?->name ?? '-' }}
                            </div>
                        @else
                            <div class="col-md-4">
                                <strong>من مخزن:</strong>
                                {{ $document->fromStock?->name ?? '-' }}
                            </div>
                            <div class="col-md-4">
                                <strong>إلى مخزن:</strong>
                                {{ $document->toStock?->name ?? '-' }}
                            </div>
                            <div class="col-md-4">
                                <strong>المخزن:</strong>
                                {{ $document->toStock?->name ?? '-' }}
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المنتج</th>
                                    <th>الوحدة</th>
                                    <th>الكمية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($type === 'purchase')
                                    @foreach ($document->details as $detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $detail->product?->name }}</td>
                                            <td>{{ $detail->unit?->name }}</td>
                                            <td>{{ number_format($detail->qty, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach ($document->transformDetails as $detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $detail->product?->name }}</td>
                                            <td>{{ $detail->unit?->name }}</td>
                                            <td>{{ number_format($detail->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        @if ($confirmation)
                            <div class="alert alert-success">
                                تم التأكيد بواسطة {{ $confirmation->user?->name ?? '-' }}
                                في {{ $confirmation->created_at?->format('Y-m-d H:i') }}
                            </div>

                            @can('إلغاء تأكيد الاستلام')
                                <form action="{{ route('receipts.unconfirm', [$type, $document->id]) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">إلغاء التأكيد</button>
                                </form>
                            @endcan
                        @else
                            @can('تأكيد استلام المخزون')
                                <form action="{{ route('receipts.confirm', [$type, $document->id]) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">تأكيد الاستلام</button>
                                </form>
                            @endcan
                        @endif

                        <a href="{{ route('receipts.index') }}" class="btn btn-warning">رجوع</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
