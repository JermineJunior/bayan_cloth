@extends('layouts.app')

@section('css')
    <style>
        .card {
            direction: ltr;
        }

        .main-content .header {
            width: 100%;
            margin: 0 !important;
            margin-top: 20px !important;
            margin-bottom: 20px !important;
        }

        .header {
            margin-bottom: 20px;
            margin-left: 5px;
        }

        /* ✅ جدول الهيدر (٣ خانات) */
        .report-header {
            width: 100%;
            border-collapse: collapse;
        }

        .report-header td {
            border: none;
            padding: 6px;
            vertical-align: middle;
        }

        .report-header td.header-left {
            width: 25%;
            text-align: left;
        }

        .report-header td.header-center {
            width: 50%;
            text-align: center;
        }

        .report-header td.header-right {
            width: 25%;
            text-align: right;
            font-size: 14px;
            line-height: 1.6;
        }

        /* ✅ اسم الشركة */
        .header h1 {
            margin: 0;
            font-size: 26px;
            /* 🔹 أكبر من 20px */
            font-weight: 800;
            text-align: center;
            color: #0d47a1;
            /* لون مميز */
            letter-spacing: 1px;
        }

        /* ✅ الوصف تحت اسم الشركة */
        .header p.title-desc {
            margin: 2px 0;
            font-size: 16px;
            font-weight: 500;
            text-align: center;
            color: #333;
        }

        .report-header img.logo {
            width: 80px;
            height: auto;
            display: block;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .price-table,
        .price-table th,
        .price-table td {
            border: 1px solid black;
        }

        /* ✅ الهيدر الخاص بالجدول */
        th {
            background-color: #e3f2fd;
            color: #0d47a1;
            font-size: 15px;
            /* 🔹 أكبر من النص العادي */
            font-weight: bold;
            text-transform: uppercase;
            text-align: center !important;
            padding: 12px;
        }

        /* ✅ بيانات الجدول */
        td {
            padding: 10px;
            text-align: center !important;
            font-size: 14px;
        }

        /* ✅ اسم الشركة */
        .company_name {
            background: #bbdefb;
            color: #0d47a1;
            font-weight: bold;
            font-size: 17px;
            text-align: left;
        }

        /* معلومات الشركة */
        .company-info {
            font-size: 16px;
            font-weight: 600;
        }

        /* معلومات المندوب */
        .rep-info {
            font-size: 16px;
            font-weight: 600;
        }

        /* اسم المنتج */
        .product-name {
            font-size: 16px;
            font-weight: 600;
        }

        /* الاسم العلمي */
        .product-generic {
            font-size: 15px;
            font-weight: 500;
        }

        /* السعر */
        .product-price {
            font-size: 16px;
            font-weight: bold;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            #global-loader,
            .app-sidebar,
            .app-sidebar__overlay,
            .main-header,
            .main-footer,
            .breadcrumb-header,
            .page-header,
            .btn {
                display: none !important;
            }

            .main-content.app-content,
            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
            }

            .card,
            .card-body {
                padding: 0 !important;
                margin: 0 !important;
                border: 0 !important;
            }

            table {
                width: 100%;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }

            th,
            td {
                white-space: nowrap;
            }

            td.product-name,
            td.product-generic {
                white-space: normal;
            }

            #print_Button {
                display: none !important;
            }
        }
    </style>
@endsection


@section('content')
    <div id="print">
        <div class="card">
            <div class="card-body">
                <!-- ✅ الهيدر -->
                <div class="header">
                    <table class="report-header">
                        <tr>
                            <!-- الشمال (شعار + بريد وهاتف) -->
                            <td class="header-left">
                                <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" class="logo" alt="الشعار">
                                @if (GeneralSiteSettings('email'))
                                    <div class="company-info">{{ GeneralSiteSettings('email') }}</div>
                                @endif
                                @if (GeneralSiteSettings('phone'))
                                    <div class="company-info">{{ GeneralSiteSettings('phone') }}</div>
                                @endif
                            </td>

                            <!-- العنوان الرئيسي -->
                            <td class="header-center">
                                <h1>{{ GeneralSiteSettings('name') }}</h1>
                            </td>

                            <!-- اليمين (تاريخ مثلاً) -->
                            <td class="header-right">
                                <p>{{ date('d/m/Y') }}</p>
                                @if ($representative)
                                    <p class="rep-info">اسم المندوب : {{ $representative->tree4_name }}</p>
                                    @if ($representative->phone)
                                        <p class="rep-info">رقم هاتف المندوب : {{ $representative->phone }}</p>
                                    @endif
                                @endif
                                @if ($selectedCategory)
                                    <p class="rep-info">القسم : {{ $selectedCategory->name }}</p>
                                @endif
                                <p style=" direction: rtl;">اسم العميل: {{ '..........................' }}</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- ✅ الجدول -->
                @php $visibleColumns = $show_expiry ? 7 : 6; @endphp
                <table class="price-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Generic name</th>
                            @if ($show_expiry)
                                <th>Expire date</th>
                            @endif
                            <th>Unit</th>
                            <th>W.Price</th>
                            {{--   <th>Order</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @php $index = 1; @endphp
                        @foreach ($categories as $category)
                            @if ($show_company)
                                <tr>
                                    <td colspan="{{ $visibleColumns }}" class="company_name">{{ $category->name }}</td>
                                </tr>
                            @endif
                            @foreach ($category->products as $product)
                                @php $unit = $product->units->sortByDesc('id')->first(); @endphp
                                <tr>
                                    <td>{{ $index++ }}</td>
                                    <td class="product-name">{{ $product->name }}</td>
                                    <td class="product-generic">{{ $product->name_en }}</td>
                                    @if ($show_expiry)
                                        <td>{{ $product->expire_date }}</td>
                                    @endif
                                    <td>{{ $unit?->name ?? '---' }}</td>
                                    <td class="product-price">
                                        {{ $unit ? number_format($unit->pivot->price) : '---' }}
                                    </td>
                                    {{--   <td></td> --}}
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <button class="btn btn-primary float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                    <i class="mdi mdi-printer ml-1"></i> طبـاعة </button>
            </div>

        </div>
    </div>
    </div>
    </div>
@endsection

@section('js')
    <script>
        function printDiv() {
            window.print();
        }
    </script>
@endsection
