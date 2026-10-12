<!-- main-sidebar -->

<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ url('/' . ($page = 'home')) }}">
            {{-- <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" class="main-logo" alt="logo"> --}}
            @if (GeneralSiteSettings('logo'))
                <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" class="main-logo" alt="logo" />
            @else
                <img src="{{ Avatar::create('phone') }}" class="main-logo" alt="logo" />
            @endif
        </a>
        <a class="desktop-logo logo-dark active" href="{{ url('/' . ($page = 'home')) }}">
            @if (GeneralSiteSettings('logo'))
                <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" class="main-logo dark-theme"
                    alt="logo">
            @else
                <img src="{{ Avatar::create('phone') }}" class="main-logo" alt="logo" />
            @endif
        </a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ url('/' . ($page = 'home')) }}">
            @if (GeneralSiteSettings('logo'))
                <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" class="logo-icon" alt="logo">
            @else
                <img src="{{ Avatar::create('phone') }}" class="main-logo" alt="logo" />
            @endif
        </a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ url('/' . ($page = 'home')) }}">
            @if (GeneralSiteSettings('logo'))
                <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" class="logo-icon dark-theme"
                    alt="logo">
            @else
                <img src="{{ Avatar::create('phone') }}" class="main-logo" alt="logo" />
            @endif
        </a>
    </div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    @if (GeneralSiteSettings('logo'))
                        <img alt="user-img" class="avatar avatar-xl brround"
                        src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}"><span
                        class="avatar-status profile-status bg-green"></span>
                    @else
                        <img alt="user-img" class="avatar avatar-xl brround"
                            src="{{ Avatar::create('phone') }}">
                    @endif
                    
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{ Auth::user()->name }}</h4>
                    <span class="mb-0 text-muted">{{ Auth::user()->email }}</span>
                </div>
            </div>
        </div>
        <ul class="side-menu">
            <li class="side-item side-item-category"> برنامج نظام المبيعات</li>
            @can('الرئيسية')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('welcome') }}"><svg xmlns="http://www.w3.org/2000/svg"
                            class="side-menu__icon" viewBox="0 0 24 24">
                            <path d="M12 3l10 9h-3v8h-6v-6H9v6H3v-8H2z" />
                        </svg>
                        <span class="side-menu__label">الرئيسية</span>
                    </a>
                </li>
            @endcan
            @canany(['قائمة الشركات', 'قائمة الفروع', 'قائمة الوحدات', 'قائمة البنوك', 'قائمة الخزن', 'قائمة العملات'])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                            class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M19 13H5v-2h14v2zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                        </svg><span class="side-menu__label"> الضبط العام</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('قائمة الشركات')
                            <li><a class="slide-item" href="{{ route('category.index') }}">قائمة الشركات</a></li>
                        @endcan
                        @can('قائمة الفروع')
                            <li><a class="slide-item" href="{{ route('branch.index') }}">قائمة الفروع</a></li>
                        @endcan
                        @can('قائمة الوحدات')
                            <li><a class="slide-item" href="{{ route('unit.index') }}">قائمة الوحدات </a></li>
                        @endcan
                        @can('قائمة البنوك')
                            <li><a class="slide-item" href="{{ route('bank.index') }}">قائمة البنوك </a></li>
                        @endcan
                        @can('قائمة الخزن')
                            <li><a class="slide-item" href="{{ route('safe.index') }}">قائمة الخزن </a></li>
                        @endcan
                        @can('قائمة العملات')
                            <li><a class="slide-item" href="{{ route('curencey.index') }}">قائمة العملات </a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany
            @canany(['قائمة المخازن', 'قائمة المنتجات', 'التحويل بين المخازن', 'الارصدة الافتتاحية للمنتجات'])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                            class="side-menu__icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                            <path
                                d="M3 3h18v2H3V3zm0 4h6v6H3V7zm2 2v2h2V9H5zm8-2h8v6h-8V7zm2 2v2h4V9h-4zM3 15h6v6H3v-6zm2 2v2h2v-2H5zm8-2h8v6h-8v-6zm2 2v2h4v-2h-4z" />
                        </svg>
                        <span class="side-menu__label">ادارة المنتجات والمخازن</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('قائمة المخازن')
                            <li><a class="slide-item" href="{{ route('stock.index') }}">قائمة المخازن</a></li>
                        @endcan
                        @can('قائمة المنتجات')
                            <li><a class="slide-item" href="{{ route('product.index') }}">قائمة المنتجات </a>
                            </li>
                        @endcan
                        @can('التحويل بين المخازن')
                            <li><a class="slide-item" href="{{ route('stock.transform.list') }}">التحويل بين المخازن</a></li>
                        @endcan
                        @can('الارصدة الافتتاحية للمنتجات')
                            <li><a class="slide-item" href="{{ route('product.opening') }}">الارصدة الافتتاحية للمنتجات</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany
            @canany(['قائمة الموردين', 'قائمة العملاء', 'قائمة المناديب'])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.31 0-6 2.69-6 6v2h12v-2c0-3.31-2.69-6-6-6zm-6 4v1h12v-1c0-2.21-1.79-4-4-4s-4 1.79-4 4z" />
                        </svg>
                        <span class="side-menu__label">الموردين والعملاء والمناديب</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('قائمة الموردين')
                            <li><a class="slide-item" href="{{ route('supplier.index') }}">قائمة الموردين </a>
                            </li>
                        @endcan
                        @can('قائمة العملاء')
                            <li><a class="slide-item" href="{{ route('customer.index') }}">قائمة العملاء </a>
                            </li>
                        @endcan
                        @can('قائمة المناديب')
                            <li><a class="slide-item" href="{{ route('representative.index') }}">قائمة المناديب </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany
            @canany([
                'فاتورة المبيعات',
                'فاتورة المشتريات',
                'فاتورة مردودات مبيعات',
                'فاتورة مردودات مشتريات',
                'طلب شراء من مورد',
                'فاتورة
                مبدئية',
                ])
                <li class="side-item side-item-category" style="font-weight: bold;"> ادارة الفواتير </li>
            @endcanany
            @canany([
                'فاتورة المبيعات',
                'فاتورة المشتريات',
                'فاتورة مردودات مبيعات',
                'فاتورة مردودات مشتريات',
                'فاتورة
                مبدئية',
                'طلب شراء من مورد'
                ])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M6 2c-.55 0-1 .45-1 1v18c0 .55.45 1 1 1h12c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H6zM7 4h10v16H7V4zm2 2h6v2H9V6zm0 4h6v2H9v-2zm0 4h6v2H9v-2z" />
                        </svg>
                        <span class="side-menu__label"> الفواتير</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('فاتورة المبيعات')
                            <li><a class="slide-item" href="{{ route('sale_invoice.index') }}"> مبيعات</a></li>
                        @endcan
                        @can('فاتورة المشتريات')
                            <li><a class="slide-item" href="{{ route('purchase_invoice.index') }}"> مشتريات </a></li>
                        @endcan
                        @can('فاتورة مردودات مبيعات')
                            <li><a class="slide-item" href="{{ route('sale_invoice_return.index') }}"> مردودات المبيعات
                                </a></li>
                        @endcan
                        @can('فاتورة مردودات مشتريات')
                            <li><a class="slide-item" href="{{ route('purchase_invoice_return.index') }}"> مردودات
                                    المشتريات </a></li>
                        @endcan
                        @can('فاتورة مبدئية')
                            <li><a class="slide-item" href="{{ route('preliminaryInvoice.index') }}"> فاتورة مبدئية </a></li>
                        @endcan
                        @can('طلب شراء من مورد')
                            <li><a class="slide-item" href="{{ route('purchase_order.index') }}"> طلب شراء من مورد </a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany
            @canany([
                'تقرير المبيعات حسب العميل',
                'تقرير راجع مبيعات حسب العميل',
                'تقرير مبيعات حسب المستخدم',
                'تقرير راجع مبيعات حسب المستخدم',
                'تقرير مبيعات حسب الشركة',
                'تقرير مردود مبيعات حسب الشركة',
                'تقرير مبيعات مفصل',
                'تقرير مردود مبيعات مفصل',
                'تقرير مبيعات ملخص',
                'تقرير مردود مبيعات ملخص',
                'تقرير المبيعات حسب المندوب',
                'تقرير مردود المبيعات حسب المندوب',
                'تقرير عمولة المندوب',
                'تقرير المشتريات حسب المورد',
                'تقرير راجع مشتريات حسب المورد',
                'تقرير مشتريات حسب المستخدم',
                'تقرير راجع مشتريات حسب المستخدم',
                'تقرير مشتريات حسب الشركة',
                'تقرير مردود مشتريات حسب الشركة',
                'تقرير مشتريات مفصل',
                'تقرير مردود مشتريات مفصل',
                'تقرير مشتريات ملخص',
                'تقرير مردود مشتريات ملخص',
                'تقرير الموقف المالي للعميل',
                'تقرير الموقف المالي للمورد',
                'كشف حساب خزنة',
                'كشف حساب بنك',
                'كشف حساب المصروفات',
                'كشف حساب الايرادات',
                'تقرير مديونيات عملاء المندوب',
                'تقرير مدينونيات العملاء',
                'تقرير المنتجات الاكثر طلبا',
                'تقرير المنتجات التي تجاوزت تاريخ الصلاحية',
                'تقرير المنتجات التي شارفت على تجاوز تاريخ الصلاحية',
                'تقرير المنتجات التي تجازوت حد الطلب',
                'تقرير مفصل للمخازن حسب الفترة',
                'تقرير حركة المخزن',
                'تقرير الجرد للمشتريات',
                'تقرير الجرد للمبيعات',
                'تقرير كشف السعر',
                ])
                <li class="side-item side-item-category" style="font-weight: bold;"> التقارير </li>

            @endcanany
            @canany([
                'تقرير المبيعات حسب العميل',
                'تقرير راجع مبيعات حسب العميل',
                'تقرير مبيعات حسب المستخدم',
                'تقرير
                راجع مبيعات حسب المستخدم',
                'تقرير مبيعات حسب الشركة',
                'تقرير مردود مبيعات حسب الشركة',
                'تقرير مبيعات مفصل',
                'تقرير مردود مبيعات مفصل',
                'تقرير مبيعات ملخص',
                'تقرير مردود مبيعات ملخص',
                'تقرير المبيعات حسب المندوب',
                'تقرير مردود المبيعات حسب المندوب',
                ])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"
                            width="24" height="24" fill="currentColor">
                            <path d="M3 17h2v-6H3v6zm4 0h2V7H7v10zm4 0h2v-4h-2v4zm4 0h2V4h-2v13zm4 0h2v-8h-2v8z" />
                        </svg>

                        <span class="side-menu__label"> تقارير المبيعات</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('تقرير المبيعات حسب العميل')
                            <li><a class="slide-item" href="{{ route('report.sale_invoice.customer.index') }}">تقرير المبيعات
                                    حسب العميل
                                </a></li>
                        @endcan
                        @can('تقرير راجع مبيعات حسب العميل')
                            <li><a class="slide-item" href="{{ route('report.sale_return_invoice.customer.index') }}">تقرير
                                    راجع مبيعات حسب العميل
                                </a></li>
                        @endcan
                        @can('تقرير مبيعات حسب المستخدم')
                            <li><a class="slide-item" href="{{ route('report.sale_invoice.agent.index') }}">تقرير مبيعات حسب
                                    المستخدم
                                </a></li>
                        @endcan
                        @can('تقرير راجع مبيعات حسب المستخدم')
                            <li><a class="slide-item" href="{{ route('report.sale_return_invoice.agent.index') }}">تقرير راجع
                                    ميبعات حسب المستخدم
                                </a></li>
                        @endcan
                        @can('تقرير مبيعات حسب الشركة')
                            <li><a class="slide-item" href="{{ route('report.sale_invoice.category.index') }}">تقرير مبيعات
                                    حسب الشركة
                                </a></li>
                        @endcan
                        @can('تقرير مردود مبيعات حسب الشركة')
                            <li><a class="slide-item" href="{{ route('report.sale_return_invoice.category.index') }}">تقرير
                                    مردود مبيعات حسب الشركة
                                </a></li>
                        @endcan
                        @can('تقرير مبيعات حسب الشركة')
                            <li><a class="slide-item" href="{{ route('report.sale_invoice.product.index') }}">تقرير مبيعات
                                    حسب الصنف
                                </a></li>
                        @endcan
                        @can('تقرير مبيعات مفصل')
                            <li><a class="slide-item" href="{{ route('report.sale_invoice.details.index') }}">تقرير مبيعات
                                    مفصل
                                </a></li>
                        @endcan
                        @can('تقرير مردود مبيعات مفصل')
                            <li><a class="slide-item" href="{{ route('report.sale_return_invoice.details.index') }}">تقرير
                                    مردود مبيعات مفصل
                                </a></li>
                        @endcan
                        @can('تقرير مبيعات ملخص')
                            <li><a class="slide-item" href="{{ route('report.sale_invoice.summary.index') }}">تقرير مبيعات
                                    ملخص
                                </a></li>
                        @endcan
                        @can('تقرير مردود مبيعات ملخص')
                            <li><a class="slide-item" href="{{ route('report.sale_return_invoice.summary.index') }}">تقرير
                                    مردود مبيعات ملخص
                                </a></li>
                        @endcan
                        @can('تقرير المبيعات حسب المندوب')
                            <li><a class="slide-item mb-3" href="{{ route('report.sale_invoice.representative.index') }}">
                                    تقرير المبيعات حسب المندوب</a></li>
                        @endcan
                        @can('تقرير مردود المبيعات حسب المندوب')
                            <li><a class="slide-item mb-3"
                                    href="{{ route('report.sale_invoice_return.representative.index') }}">
                                    تقرير مردود المبيعات حسب المندوب</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @canany([
                'تقرير المشتريات حسب المورد',
                'تقرير راجع مشتريات حسب المورد',
                'تقرير مشتريات حسب المستخدم',
                'تقرير
                راجع مشتريات حسب المستخدم',
                'تقرير مشتريات حسب الشركة',
                'تقرير مردود مشتريات حسب الشركة',
                'تقرير مشتريات
                مفصل',
                'تقرير مردود مشتريات مفصل',
                'تقرير مشتريات ملخص',
                'تقرير مردود مشتريات ملخص',
                ])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"
                            width="24" height="24" fill="currentColor">
                            <path
                                d="M21 2H3v20l3-3 3 3 3-3 3 3 3-3 3 3V2zM5 4h14v13.17l-1.59-1.59L16 17l-2.41-2.41L11 17l-2.41-2.41L6 17l-1-1V4z" />
                        </svg>
                        <span class="side-menu__label"> تقارير المشتريات</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('تقرير المشتريات حسب المورد')
                            <li><a class="slide-item" href="{{ route('report.purchase_ivoice.supplier.index') }}">تقرير
                                    المشتريات حسب المورد
                                </a></li>
                        @endcan
                        @can('تقرير راجع مشتريات حسب المورد')
                            <li><a class="slide-item"
                                    href="{{ route('report.purchase_return_invoice.supplier.index') }}">تقرير راجع المشتريات
                                    حسب المورد
                                </a></li>
                        @endcan
                        @can('تقرير مشتريات حسب المستخدم')
                            <li><a class="slide-item" href="{{ route('report.purchase_ivoice.agent.index') }}">تقرير
                                    المشتريات حسب المستخدم
                                </a></li>
                        @endcan
                        @can('تقرير راجع مشتريات حسب المستخدم')
                            <li><a class="slide-item" href="{{ route('report.purchase_return_invoice.agent.index') }}">تقرير
                                    راجع مشتريات حسب المستخدم
                                </a></li>
                        @endcan
                        @can('تقرير مشتريات حسب الشركة')
                            <li><a class="slide-item" href="{{ route('report.purchase_invoice.category.index') }}">تقرير
                                    مشتريات حسب الشركة
                                </a></li>
                        @endcan
                        @can('تقرير مردود مشتريات حسب الشركة')
                        <li><a class="slide-item"
                            href="{{ route('report.purchase_return_invoice.category.index') }}">تقرير مردود مشتريات
                            حسب الشركة
                        </a></li>
                        @endcan
                        @can('تقرير مشتريات حسب الشركة')
                            <li><a class="slide-item" href="{{ route('report.purchase_invoice.product.index') }}">تقرير مشتريات
                                    حسب الصنف
                                </a></li>
                        @endcan
                        @can('تقرير مشتريات مفصل')
                            <li><a class="slide-item" href="{{ route('report.purchase_invoice.details.index') }}">تقرير
                                    مشتريات مفصل
                                </a></li>
                        @endcan
                        @can('تقرير مردود مشتريات مفصل')
                            <li><a class="slide-item"
                                    href="{{ route('report.purchase_return_invoice.details.index') }}">تقرير مردود مشتريات
                                    مفصل
                                </a></li>
                        @endcan
                        @can('تقرير مشتريات ملخص')
                            <li><a class="slide-item" href="{{ route('report.purchase_invoice.summary.index') }}">تقرير
                                    مشتريات ملخص
                                </a></li>
                        @endcan
                        @can('تقرير مردود مشتريات ملخص')
                            <li><a class="slide-item"
                                    href="{{ route('report.purchase_return_invoice.summary.index') }}">تقرير مردود مشتريات
                                    ملخص
                                </a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany([
                'تقرير الموقف المالي للعميل',
                'تقرير الموقف المالي للمورد',
                'كشف حساب خزنة',
                'كشف حساب بنك',
                'كشف
                حساب الايرادات',
                'كشف حساب المصروفات',
                'تقرير مدينونيات العملاء',
                ])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"
                            width="24" height="24" fill="currentColor">
                            <path d="M3 21h18v2H3zm4-4h2v-8H7zm4-4h2v-4h-2zm4-2h2V7h-2z" />
                        </svg>
                        <span class="side-menu__label"> التقارير المالية</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('تقرير الموقف المالي للعميل')
                            <li><a class="slide-item mb-3" href="{{ route('report.customer.account') }}">
                                    تقرير الموقف المالي للعميل</a></li>
                        @endcan
                        @can('تقرير الموقف المالي للمورد')
                            <li><a class="slide-item mb-3" href="{{ route('report.supplier.account') }}">
                                    تقرير الموقف المالي للمورد</a></li>
                        @endcan
                        @can('كشف حساب خزنة')
                            <li><a class="slide-item mb-3" href="{{ route('report.safe.account') }}">
                                    كشف حساب خزنة</a></li>
                        @endcan
                        @can('كشف حساب بنك')
                            <li><a class="slide-item mb-3" href="{{ route('report.bank.account') }}">
                                    كشف حساب بنك</a></li>
                        @endcan
                        @can('كشف حساب المصروفات')
                            <li><a class="slide-item mb-3" href="{{ route('report.expense') }}">
                                    تقرير كشف المصروفات </a></li>
                        @endcan
                        @can('كشف حساب الايرادات')
                            <li><a class="slide-item mb-3" href="{{ route('report.revenue') }}">
                                    تقرير كشف الايرادات </a></li>
                        @endcan
                        @can('تقرير مدينونيات العملاء')
                            <li><a class="slide-item mb-3" href="{{ route('report.customer.account.index') }}">
                                    تقرير مدينونيات العملاء </a></li>
                        @endcan

                    </ul>
                </li>
            @endcanany

            @canany([
                'تقرير المبيعات حسب المندوب',
                'تقرير مردود المبيعات حسب المندوب',
                'تقرير عمولة المندوب',
                'تقرير مديونيات عملاء المندوب',
                ])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"
                            width="24" height="24" fill="currentColor">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                        </svg>
                        <span class="side-menu__label"> تقارير المندوبين</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('تقرير المبيعات حسب المندوب')
                            <li><a class="slide-item mb-3" href="{{ route('report.sale_invoice.representative.index') }}">
                                    تقرير المبيعات حسب المندوب</a></li>
                        @endcan
                        @can('تقرير مردود المبيعات حسب المندوب')
                            <li><a class="slide-item mb-3"
                                    href="{{ route('report.sale_invoice_return.representative.index') }}">
                                    تقرير مردود المبيعات حسب المندوب</a></li>
                        @endcan
                        @can('تقرير عمولة المندوب')
                            <li><a class="slide-item mb-3" href="{{ route('report.representative.cut.index') }}">
                                    تقرير عمولة المندوب</a></li>
                        @endcan
                        @can('تقرير مديونيات عملاء المندوب')
                            <li><a class="slide-item mb-3" href="{{ route('report.representative.customer.index') }}">
                                    تقرير مديونيات عملاء المندوب</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany


            @canany([
                'تقرير المنتجات الاكثر طلبا',
                'تقرير المنتجات التي تجاوزت تاريخ الصلاحية',
                'تقرير المنتجات التي
                شارفت على تجاوز تاريخ الصلاحية',
                'تقرير المنتجات التي تجازوت حد الطلب',
                'تقرير مفصل للمخازن حسب الفترة',
                'نقرير حركة المخزن',
                'تقرير الجرد للمشتريات',
                'تقرير الجرد للمبيعات',
                'تقرير كشف السعر',
                ])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"
                            width="24" height="24" fill="currentColor">
                            <path
                                d="M20.54 5.23L12 1 3.46 5.23a1 1 0 0 0-.46.84v11.86a1 1 0 0 0 .6.92l8.4 3.44a1 1 0 0 0 .8 0l8.4-3.44a1 1 0 0 0 .6-.92V6.07a1 1 0 0 0-.46-.84zM12 3.25L18.18 6 12 8.75 5.82 6 12 3.25zM5 7.96l6 2.45v9.63L5 17.6V7.96zm14 9.64-6 2.44v-9.63l6-2.45v9.64z" />
                        </svg>

                        <span class="side-menu__label">تقارير المخازن</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('تقرير المنتجات الاكثر طلبا')
                            <li><a class="slide-item" href="{{ route('report.top-products') }}">تقرير المنتجات الاكثر
                                    مبيعا </a></li>
                        @endcan
                        @can('تقرير المنتجات التي تجاوزت تاريخ الصلاحية')
                            <li><a class="slide-item mb-3" href="{{ route('report.expired-products') }}">تقرير المنتجات التي
                                    تجاوزت تاريخ الصلاحية </a></li>
                        @endcan
                        @can('تقرير المنتجات التي شارفت على تجاوز تاريخ الصلاحية')
                            <li><a class="slide-item mb-3" href="{{ route('report.expiring-products') }}">تقرير المنتجات
                                    التي
                                    شارفت على تجاوز تاريخ الصلاحية </a></li>
                        @endcan
                        @can('تقرير المنتجات التي تجازوت حد الطلب')
                            <li><a class="slide-item mb-3" href="{{ route('report.low_stock') }}">تقرير المنتجات التي
                                    تجاوزت حد الطلب </a></li>
                        @endcan
                        @can('تقرير مفصل للمخازن حسب الفترة')
                            <li><a class="slide-item mb-3" href="{{ route('report.stock.details.index') }}">تقرير مفصل
                                    للمخازن
                                    حسب الفترة </a></li>
                        @endcan
                        @can('تقرير حركة المخزن')
                            <li><a class="slide-item mb-3" href="{{ route('burren.info') }}">تقرير حركة المخزن </a></li>
                        @endcan
                        @can('تقرير كشف السعر')
                            <li><a class="slide-item mb-3" href="{{ route('select.representative') }}">تقرير كشف السعر </a></li>
                        @endcan
                        @can('تقرير الجرد للمبيعات')
                            <li><a class="slide-item mb-3" href="{{ route('report.burren.sale_price.index') }}">تقرير الجرد للمبيعات </a></li>
                        @endcan
                         @can('تقرير الجرد للمشتريات')
                            <li><a class="slide-item mb-3" href="{{ route('report.burren.purchase_price.index') }}">تقرير الجرد للمشتريات </a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany



            @canany([
                'الايردادات',
                'تحصيل فواتير العملاء',
                'قيود مالية اخرى',
                'المصروفات',
                'القيود المركبة',
                'قائمة القيود',
                'تقرير كشف حساب',
                'تقرير دفتر اليومية',
                'تقرير القيود',
                'الدفاتر المحاسبية',
                'المستوى الاول',
                'المستوى الثاني',
                'المستوى
                الثالث',
                'المستوى الرابع',
                'ادارة الارصدة الافتتاحية',
                ])
                <li class="side-item side-item-category" style="font-weight: bold;"> الشجرة المحاسبية </li>

            @endcanany
            @canany(['الايردادات', 'تحصيل فواتير العملاء', 'قيود مالية اخرى', 'المصروفات', 'القيود المركبة', 'قائمة القيود'])

                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                        </svg>
                        <span class="side-menu__label">إدارة المالية</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('الايردادات')
                            <li><a class="slide-item" href="{{ route('revenue.index') }}"> الايردادات</a></li>
                        @endcan
                        @can('تحصيل فواتير العملاء')
                            <li><a class="slide-item" href="{{ route('customer-invoice-payment.create') }}"> تحصيل فواتير العملاء</a></li>
                        @endcan 
                        @can('المصروفات')
                            <li><a class="slide-item" href="{{ route('expense.index') }}"> المصروفات</a></li>
                        @endcan
                        @can('قيود مالية اخرى')
                            <li><a class="slide-item" href="{{ route('daily.create') }}">قيود مالية اخرى</a></li>
                        @endcan
                        @can('القيود المركبة')
                            <li><a class="slide-item" href="{{ route('compound.create') }}">القيود المركبة </a></li>
                        @endcan
                        @can('قائمة القيود')
                            <li><a class="slide-item" href="{{ route('daily.index') }}"> قائمة القيود </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany
            @canany(['تقرير كشف حساب', 'تقرير دفتر اليومية', 'تقرير القيود', 'الدفاتر المحاسبية'])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14h-2v-2h2v2zm0-4h-2V7h2v5zm4 4h-2v-2h2v2zm0-4h-2V7h2v5z" />
                        </svg>

                        <span class="side-menu__label">التقارير المحاسبية</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('تقرير كشف حساب')
                            <li><a class="slide-item" href="{{ route('account.statement') }}"> تقرير كشف حسابات </a>
                            </li>
                        @endcan
                        @can('تقرير دفتر اليومية')
                            <li><a class="slide-item" href="{{ route('journal.index') }}"> تقرير دفتر اليومية </a></li>
                        @endcan
                        @can('تقرير القيود')
                            <li><a class="slide-item" href="{{ route('journalEntry.index') }}"> تقرير القيود </a>
                            </li>
                        @endcan
                        @can('الدفاتر المحاسبية')
                            <li><a class="slide-item" href="{{ route('accountingBooks.index') }}"> الدفاتر المحاسبية </a>
                            </li>
                        @endcan
                        {{--  <li><a class="slide-item" href="{{ url('/' . $page='Operation') }}">الارصدة الافتتاحية </a></li>   --}}
                    </ul>
                </li>
            @endcanany

            @canany(['المستوى الاول', 'المستوى الثاني', 'المستوى الثالث', 'المستوى الرابع', 'ادارة الارصدة الافتتاحية'])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z" />
                        </svg>

                        <span class="side-menu__label">الدليل المحاسبي(فتح الحسابات)</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('المستوى الاول')
                            <li><a class="slide-item" href="{{ route('tree1.index') }}">المستوى الاول </a></li>
                        @endcan
                        @can('المستوى الثاني')
                            <li><a class="slide-item" href="{{ route('tree2.index') }}">المستوى الثاني </a></li>
                        @endcan
                        @can('المستوى الثالث')
                            <li><a class="slide-item" href="{{ route('tree3.index') }}">المستوى الثالث </a></li>
                        @endcan
                        @can('المستوى الرابع')
                            <li><a class="slide-item" href="{{ route('tree4.index') }}">المستوى الرابع </a></li>
                        @endcan
                        @can('ادارة الارصدة الافتتاحية')
                            <li><a class="slide-item" href="{{ route('opening.index') }}"> إدارة الأرصدة
                                    الأفتتاحية
                                </a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany
            @canany(['قائمة المستخدمين', 'المستخدمين المؤرشفين', 'صلاحيات المستخدمين'])
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
                            <path
                                d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
                        </svg><span class="side-menu__label">المستخدمين والصلاحيات</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('قائمة المستخدمين')
                            <li><a class="slide-item" href="{{ route('user.index') }}">قائمة المستخدمين</a></li>
                        @endcan
                        @can('المستخدمين المؤرشفين')
                            <li><a class="slide-item" href="{{ route('user.trashed_users') }}"> المستخدمين المؤرشفين</a>
                            </li>
                        @endcan
                        @can('صلاحيات المستخدمين')
                            <li><a class="slide-item" href="{{ route('role.index') }}">صلاحيات المستخدمين </a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @can('اعدادات النظام')
                <li class="side-item side-item-category">الإعدادات</li>
            @endcan
            @can('اعدادات النظام')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path d="M5 5h15v3H5zm12 5h3v9h-3zm-7 0h5v9h-5zm-5 0h3v9H5z" opacity=".3" />
                            <path
                                d="M20 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h15c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM8 19H5v-9h3v9zm7 0h-5v-9h5v9zm5 0h-3v-9h3v9zm0-11H5V5h15v3z" />
                        </svg><span class="side-menu__label"> إعدادات النظام </span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('اعدادات النظام')
                            <li><a class="slide-item" href="{{ route('setting.index') }}">الإعدادات العامة </a></li>
                        @endcan
                    </ul>
                </li>
            @endcan
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
