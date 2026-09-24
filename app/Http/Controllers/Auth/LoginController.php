<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $branches = $user->branches;

        if ($branches->count() === 1) {
            session(['branch_id' => $branches->first()->id]);
        } elseif ($branches->count() > 1) {
            return redirect()->route('branch.select');
        } else {
            auth()->logout();
            return redirect()
                ->route('login')
                ->withErrors([$this->username() => 'لا يوجد فرع مرتبط بهذا المستخدم.']);
        }

        if ($user->can('الرئيسية')) {
            return redirect()->route('welcome');
        } elseif ($user->can('قائمة الشركات')) {
            return redirect()->route('category.index');
        } elseif ($user->can('قائمة الوحدات')) {
            return redirect()->route('unit.index');
        } elseif ($user->can('قائمة البنوك')) {
            return redirect()->route('bank.index');
        } elseif ($user->can('قائمة الخزن')) {
            return redirect()->route('safe.index');
        } elseif ($user->can('قائمة المخازن')) {
            return redirect()->route('stock.index');
        } elseif ($user->can('قائمة المنتجات')) {
            return redirect()->route('product.index');
        } elseif ($user->can('التحويل بين المخازن')) {
            return redirect()->route('stock.transform.list');
        } elseif ($user->can('الارصدة الافتتاحية للمنتجات')) {
            return redirect()->route('product.opening');
        } elseif ($user->can('قائمة الموردين')) {
            return redirect()->route('supplier.index');
        } elseif ($user->can('قائمة العملاء')) {
            return redirect()->route('customer.index');
        } elseif ($user->can('قائمة المناديب')) {
            return redirect()->route('representative.index');
        } elseif ($user->can('فاتورة المبيعات')) {
            return redirect()->route('sale_invoice.index');
        } elseif ($user->can('فاتورة المشتريات')) {
            return redirect()->route('purchase_invoice.index');
        } elseif ($user->can('فاتورة مردودات مبيعات')) {
            return redirect()->route('sale_invoice_return.index');
        } elseif ($user->can('فاتورة مردودات مشتريات')) {
            return redirect()->route('purchase_invoice_return.index');
        } elseif ($user->can('فاتورة مبدئية')) {
            return redirect()->route('preliminaryInvoice.index');
        } elseif ($user->can('تقرير المبيعات حسب العميل')) {
            return redirect()->route('report.sale_invoice.customer.index');
        } elseif ($user->can('تقرير راجع مبيعات حسب العميل')) {
            return redirect()->route('report.sale_return_invoice.customer.index');
        } elseif ($user->can('تقرير مبيعات حسب المستخدم')) {
            return redirect()->route('report.sale_invoice.agent.index');
        } elseif ($user->can('تقرير راجع مبيعات حسب المستخدم')) {
            return redirect()->route('report.sale_return_invoice.agent.index');
        } elseif ($user->can('تقرير مبيعات حسب الشركة')) {
            return redirect()->route('report.sale_invoice.category.index');
        } elseif ($user->can('تقرير مردود مبيعات حسب الشركة')) {
            return redirect()->route('report.sale_return_invoice.category.index');
        } elseif ($user->can('تقرير مبيعات مفصل')) {
            return redirect()->route('report.sale_invoice.details.index');
        } elseif ($user->can('تقرير مردود مبيعات مفصل')) {
            return redirect()->route('report.sale_return_invoice.details.index');
        } elseif ($user->can('تقرير مبيعات ملخص')) {
            return redirect()->route('report/sale_invoice/summary');
        } elseif ($user->can('تقرير مردود مبيعات ملخص')) {
            return redirect()->route('report/sale_return_invoice/summary');
        } elseif ($user->can('تقرير المبيعات حسب المندوب')) {
            return redirect()->route('report.sale_invoice.representative.index');
        } elseif ($user->can('تقرير مردود المبيعات حسب المندوب')) {
            return redirect()->route('report.sale_invoice_return.representative.index');
        } elseif ($user->can('تقرير المشتريات حسب المورد')) {
            return redirect()->route('report.purchase_ivoice.supplier.index');
        } elseif ($user->can('تقرير راجع مشتريات حسب المورد')) {
            return redirect()->route('report.purchase_return_invoice.supplier.index');
        } elseif ($user->can('تقرير مشتريات حسب المستخدم')) {
            return redirect()->route('report.purchase_return_invoice.supplier.index');
        } elseif ($user->can('تقرير راجع مشتريات حسب المستخدم')) {
            return redirect()->route('report.purchase_return_invoice.agent.index');
        } elseif ($user->can('تقرير مشتريات حسب الشركة')) {
            return redirect()->route('report/purchase_invoice/category');
        } elseif ($user->can('تقرير مردود مشتريات حسب الشركة')) {
            return redirect()->route('report.purchase_return_invoice.category.index');
        } elseif ($user->can('تقرير مشتريات مفصل')) {
            return redirect()->route('report.purchase_invoice.details.index');
        } elseif ($user->can('تقرير مردود مشتريات مفصل')) {
            return redirect()->route('report.purchase_return_invoice.details.index');
        } elseif ($user->can('تقرير مشتريات ملخص')) {
            return redirect()->route('report.purchase_invoice.summary.index');
        } elseif ($user->can('تقرير مردود مشتريات ملخص')) {
            return redirect()->route('report.purchase_return_invoice.summary.index');
        } elseif ($user->can('تقرير الموقف المالي للعميل')) {
            return redirect()->route('report.customer.account');
        } elseif ($user->can('تقرير الموقف المالي للمورد')) {
            return redirect()->route('report.supplier.account');
        } elseif ($user->can('كشف حساب خزنة')) {
            return redirect()->route('report.safe.account');
        } elseif ($user->can('كشف حساب بنك')) {
            return redirect()->route('report.bank.account');
        } elseif ($user->can('كشف حساب المصروفات')) {
            return redirect()->route('report.expense');
        } elseif ($user->can('كشف حساب الايرادات')) {
            return redirect()->route('report.revenue');
        } elseif ($user->can('تقرير مديونيات عملاء المندوب')) {
            return redirect()->route('report.representative.customer.index');
        } elseif ($user->can('تقرير المنتجات الاكثر طلبا')) {
            return redirect()->route('report.top-products');
        } elseif ($user->can('تقرير المنتجات التي تجاوزت تاريخ الصلاحية')) {
            return redirect()->route('report.expired-products');
        } elseif ($user->can('تقرير المنتجات التي شارفت على تجاوز تاريخ الصلاحية')) {
            return redirect()->route('report.expiring-products');
        } elseif ($user->can('تقرير المنتجات التي تجازوت حد الطلب')) {
            return redirect()->route('report.low_stock');
        } elseif ($user->can('تقرير مفصل للمخازن حسب الفترة')) {
            return redirect()->route('report.stock.details.index');
        } elseif ($user->can('تقرير حركة المخزن')) {
            return redirect()->route('burren.show');
        } elseif ($user->can('تقرير الجرد')) {
            return redirect()->route('report.burren.index');
        } elseif ($user->can('تقرير كشف السعر')) {
            return redirect()->route('product.price');
        } elseif ($user->can('الايردادات')) {
            return redirect()->route('revenue.index');
        } elseif ($user->can('المصروفات')) {
            return redirect()->route('expense.index');
        } elseif ($user->can('قيود مالية اخرى')) {
            return redirect()->route('daily.create');
        } elseif ($user->can('القيود المركبة')) {
            return redirect()->route('compound.create');
        } elseif ($user->can('قائمة القيود')) {
            return redirect()->route('daily.index');
        } elseif ($user->can('تقرير كشف حساب')) {
            return redirect()->route('account.statement');
        } elseif ($user->can('تقرير دفتر اليومية')) {
            return redirect()->route('journal.index');
        } elseif ($user->can('تقرير القيود')) {
            return redirect()->route('journalEntry.index');
        } elseif ($user->can('الدفاتر المحاسبية')) {
            return redirect()->route('accountingBooks.index');
        } elseif ($user->can('المستوى الاول')) {
            return redirect()->route('tree1.index');
        } elseif ($user->can('المستوى الثاني')) {
            return redirect()->route('tree2.index');
        } elseif ($user->can('المستوى الثالث')) {
            return redirect()->route('tree3.index');
        } elseif ($user->can('المستوى الرابع')) {
            return redirect()->route('tree4.index');
        } elseif ($user->can('ادارة الارصدة الافتتاحية')) {
            return redirect()->route('opening.index');
        } elseif ($user->can('صلاحيات المستخدمين')) {
            return redirect()->route('user.trashed_users');
        } elseif ($user->can('المستخدمين المؤرشفين')) {
            return redirect()->route('role.index');
        } elseif ($user->can('قائمة المستخدمين')) {
            return redirect()->route('user.index');
        } elseif ($user->can('اعدادات النظام')) {
            return redirect()->route('setting.index');
        } else {
            return dd('عفوا لا توجد لديك صلاحية');
        }
    }

    public function username()
    {
        return 'username';
    }
}
