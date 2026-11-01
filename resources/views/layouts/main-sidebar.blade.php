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
                            <li><a class="slide-item" href="{{ route('category.index') }}">قائمة الاصناف</a></li>
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
            @php($reportPermissions = collect(config('reports_menu'))->flatMap(fn($category) => collect($category['items'])->pluck('can'))->unique()->all())
            @canany($reportPermissions)
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('reports.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"
                            width="24" height="24" fill="currentColor">
                            <path d="M3 17h2v-6H3v6zm4 0h2V7H7v10zm4 0h2v-4h-2v4zm4 0h2V4h-2v13zm4 0h2v-8h-2v8z" />
                        </svg>
                        <span class="side-menu__label">التقارير</span>
                    </a>
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
            @canany(['الايردادات', 'تحصيل فواتير العملاء', 'قيود مالية اخرى', 'المصروفات', 'القيود المركبة', 'تحويل العملات', 'قائمة القيود'])

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
                        @can('تحويل العملات')
                            <li><a class="slide-item" href="{{ route('currency-transfer.create') }}">تحويل العملات </a></li>
                        @endcan
                        @can('قائمة القيود')
                            <li><a class="slide-item" href="{{ route('daily.index') }}"> قائمة القيود </a>
                            </li>
                        @endcan
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
