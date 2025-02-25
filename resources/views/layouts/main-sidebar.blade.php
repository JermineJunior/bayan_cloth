<!-- main-sidebar -->

<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ url('/' . ($page = 'home')) }}"><img
                src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" class="main-logo" alt="logo"></a>
        <a class="desktop-logo logo-dark active" href="{{ url('/' . ($page = 'home')) }}"><img
                src="{{ URL::asset('assets/img/brand/logo-white.png') }}" class="main-logo dark-theme"
                alt="logo"></a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ url('/' . ($page = 'home')) }}"><img
                src="{{ URL::asset('assets/img/brand/favicon.png') }}" class="logo-icon" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ url('/' . ($page = 'home')) }}"><img
                src="{{ URL::asset('assets/img/brand/favicon-white.png') }}" class="logo-icon dark-theme"
                alt="logo"></a>
    </div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround"
                        src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}"><span
                        class="avatar-status profile-status bg-green"></span>
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
            @can('الضبط العام')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                            class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M19 13H5v-2h14v2zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                        </svg><span class="side-menu__label"> الضبط العام</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('قائمة الاقسام')
                            <li><a class="slide-item" href="{{ route('category.index') }}">قائمة الاقسام</a></li>
                        @endcan
                        @can('قائمة المخزون')
                            <li><a class="slide-item" href="{{ route('stock.index') }}">قائمة المخزون</a></li>
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
                    </ul>
                </li>
            @endcan
            @can('المنتجات')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M19 3H5c-1.1 0-1.99.9-1.99 2L3 19c0 1.1.89 2 1.99 2h14c1.1 0 1.99-.9 1.99-2l.01-14c0-1.1-.89-2-1.99-2zm-7 14H8v-2h4v2zm4-4H8v-2h8v2zm4-4H8V7h12v2z" />
                        </svg>
                        <span class="side-menu__label"> ادارة المنتجات</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('قائمة المنتجات')
                            <li><a class="slide-item" href="{{ route('product.index') }}">قائمة المنتجات </a>
                            </li>
                        @endcan
                        @can('اضافة منتج')
                            <li><a class="slide-item" href="{{ route('product.create') }}">إضافة منتج جديد</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('الموردين والعملاء')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.31 0-6 2.69-6 6v2h12v-2c0-3.31-2.69-6-6-6zm-6 4v1h12v-1c0-2.21-1.79-4-4-4s-4 1.79-4 4z" />
                        </svg>
                        <span class="side-menu__label"> ادارة الموردين والعملاء</span><i
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
                    </ul>
                </li>
            @endcan

            @can('الفواتير')
                <li class="side-item side-item-category" style="font-weight: bold;"> ادارة الفواتير </li>
            @endcan
            @can('الفواتير')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M6 2c-.55 0-1 .45-1 1v18c0 .55.45 1 1 1h12c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H6zM7 4h10v16H7V4zm2 2h6v2H9V6zm0 4h6v2H9v-2zm0 4h6v2H9v-2z" />
                        </svg>
                        <span class="side-menu__label"> الفواتير</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('فاتورة المبيعات')
                            <li><a class="slide-item" href="{{ route('sale_invoice.index') }}">فواتير المبيعات</a></li>
                        @endcan
                        @can('اضافة فاتورة مبيعات')
                            <li><a class="slide-item" href="{{ route('sale_invoice.create') }}">فاتورة مبيعات جديدة</a></li>
                        @endcan
                        @can('فاتورة المشتريات')
                            <li><a class="slide-item" href="{{ route('purchase_invoice.index') }}">فواتير المشتريات </a></li>
                        @endcan
                        @can('اضافة فاتورة مشتريات')
                            <li><a class="slide-item" href="{{ route('purchase_invoice.create') }}">فاتورة مشتريات جديدة</a>
                            </li>
                        @endcan
                        @can('اضافة فاتورة مبدئية')
                            <li><a class="slide-item" href="{{ route('preliminaryInvoice.create') }}">فاتورة مبدئية جديدة</a>
                            </li>
                        @endcan
                        @can('فاتورة مبدئية')
                            <li><a class="slide-item" href="{{ route('preliminaryInvoice.index') }}">فواتير مبدئية </a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('تقارير نظام المبيعات')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"
                            width="24" height="24" fill="currentColor">
                            <path d="M3 21h18v2H3zm4-4h2v-8H7zm4-4h2v-4h-2zm4-2h2V7h-2z" />
                        </svg>
                        <span class="side-menu__label"> التقارير</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('تقرير المبيعات اليومية')
                            <li><a class="slide-item" href="{{ route('reports.daily_sales') }}">تقرير المبيعات اليومية</a>
                            </li>
                        @endcan
                        @can('تقرير المبيعات الشهرية')
                            <li><a class="slide-item" href="{{ route('reports.monthly_sales') }}">تقرير المبيعات الشهرية</a>
                            </li>
                        @endcan
                        @can('تقرير المبيعات السنوية')
                            <li><a class="slide-item" href="{{ route('reports.yearly_sales') }}">تقرير المبيعات السنوية
                                </a></li>
                        @endcan
                        @can('تقرير المشتريات اليومية')
                            <li><a class="slide-item" href="{{ route('reports.daily_purchases') }}">تقرير المشتريات اليومية</a>
                            </li>
                        @endcan
                        @can('تقرير المشتريات الشهرية')
                            <li><a class="slide-item" href="{{ route('reports.monthly_purchases') }}">تقرير المشتريات
                                    الشهرية</a>
                            </li>
                        @endcan
                        @can('تقرير المشتريات السنوية')
                            <li><a class="slide-item" href="{{ route('reports.yearly_purchases') }}">تقرير المشتريات السنوية
                                </a></li>
                        @endcan
                        
                        @can('تقرير المبيعات')
                            <li><a class="slide-item" href="{{ route('reports.sale.index') }}">تقرير المبيعات
                                </a></li>
                        @endcan
                        @can('تقرير المشتريات')
                            <li><a class="slide-item" href="{{ route('reports.purchase.index') }}">تقرير المشتريات
                                </a></li>
                        @endcan
                        @can('تقرير مردودات المبيعات')
                            <li><a class="slide-item" href="{{ route('reports.sale_return.index') }}">تقرير مردودات المبيعات
                                </a></li>
                        @endcan
                        @can('تقرير مردودات المشتريات')
                            <li><a class="slide-item" href="{{ route('reports.purchase_return.index') }}">تقرير مردودات المشتربات
                                </a></li>
                        @endcan
                        @can('تقرير المبيعات على حسب العميل')
                            <li><a class="slide-item" href="{{ route('reports.customer_sales') }}">تقرير المبيعات حسب
                                    العميل </a></li>
                        @endcan
                        @can('تقرير المشتريات على حسب المورد')
                            <li><a class="slide-item" href="{{ route('reports.supplier_purchases') }}">تقرير المشتربات حسب
                                    المورد </a></li>
                        @endcan
                        @can('تقرير المنتجات الاكثر طلبا')
                            <li><a class="slide-item" href="{{ route('reports.top-products') }}">تقرير المنتجات الاكثر
                                    مبيعا </a></li>
                        @endcan
                        @can('تقرير المنتجات التي تجاوزت تاريخ الصلاحية')
                            <li><a class="slide-item mb-3" href="{{ route('reports.expired-products') }}">تقرير المنتجات التي
                                    تجاوزت تاريخ الصلاحية </a></li>
                        @endcan
                        @can('تقرير المنتجات التي شارفت على تجاوز تاريخ الصلاحية')
                            <li><a class="slide-item mb-3" href="{{ route('reports.expiring-products') }}">تقرير المنتجات التي
                                    شارفت على تجاوز تاريخ الصلاحية </a></li>
                        @endcan
                        @can('تقرير المنتجات التي تجازوت حد الطلب')
                            <li><a class="slide-item mb-3" href="{{ route('reports.low_stock') }}">تقرير المنتجات التي
                                    تجاوزت حد الطلب </a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('الادارة المالية')
                <li class="side-item side-item-category" style="font-weight: bold;"> الادارة المالية للنظام </li>
            @endcan
            @can('الادارة المالية')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                        </svg>
                        <span class="side-menu__label">إدارة المالية</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @can('القيود اليومية')
                            <li><a class="slide-item" href="{{ route('daily.create') }}">قيود اليومية</a></li>
                        @endcan
                        @can('القيود المركبة')
                            <li><a class="slide-item" href="{{ route('compound.create') }}">القيود المركبة </a></li>
                        @endcan
                        @can('ادراة قيود اليومية')
                            <li><a class="slide-item" href="{{ route('daily.index') }}"> إدارة قيود اليومية </a>
                            </li>
                        @endcan
                        @can('الارصدة الافتتاحية')
                            <li><a class="slide-item" href="{{ route('opening.create') }}">إضافة رصيد إفتتاحي </a>
                            </li>
                        @endcan
                        @can('ادراة الارصدة الافتتاحية')
                            <li><a class="slide-item" href="{{ route('opening.index') }}"> إدارة الأرصدة
                                    الأفتتاحية
                                </a></li>
                        @endcan
                        {{--  <li><a class="slide-item" href="{{ url('/' . $page='Restrictions') }}"> القيود العامة </a></li>  --}}
                    </ul>
                </li>
            @endcan
            @can('التقارير المحاسبية')
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
                        @can('دفتر اليومية')
                            <li><a class="slide-item" href="{{ route('journal.index') }}"> دفتر اليومية </a></li>
                        @endcan
                        @can('تقرير قيد يومية')
                            <li><a class="slide-item" href="{{ route('journalEntry.index') }}"> تقرير قيد يومية </a>
                            </li>
                        @endcan
                        @can('الدفاتر المحاسبية')
                            <li><a class="slide-item" href="{{ route('accountingBooks.index') }}"> الدفاتر المحاسبية </a>
                            </li>
                        @endcan
                        {{--  <li><a class="slide-item" href="{{ url('/' . $page='Operation') }}">الارصدة الافتتاحية </a></li>   --}}
                    </ul>
                </li>
            @endcan

            @can('الانشطة المحــاسبية')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z" />
                        </svg>

                        <span class="side-menu__label">الأنشطة المحــاسبية</span><i
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
                    </ul>
                </li>
            @endcan
            @can('المستخدمين والصلاحيات')
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
                            <li><a class="slide-item" href="{{ route('user.trashed_users') }}"> المستخدمين المؤرشفين</a></li>
                        @endcan
                        @can('صلاحيات المستخدمين')
                            <li><a class="slide-item" href="{{ route('role.index') }}">صلاحيات المستخدمين </a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            {{-- @can('اعدادات النظام')
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
                        @can('الإعدادات العامة')
                            <li><a class="slide-item" href="{{ url('/' . ($page = 'Setting')) }}">الإعدادات العامة </a></li>
                        @endcan
                    </ul>
                </li>
            @endcan --}}
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
