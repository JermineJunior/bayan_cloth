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

        /* جدول الهيدر (٣ خانات: شعار | اسم الشركة | شعار) */
        .report-header {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
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
        }

        /* اسم الشركة */
        .header h1 {
            margin: 0;
            font-size: 30px;
            font-weight: bold;
            text-align: center;
            color: #0d47a1;
            letter-spacing: 1px;
        }

        .report-header img.logo {
            width: 110px;
            max-width: 110px;
            max-height: 110px;
            height: auto;
            display: inline-block;
            vertical-align: middle;
            margin-bottom: 0;
        }

        /* صندوق التاريخ */
        .date-wrap {
            text-align: center;
            margin: 8px 0 6px;
        }

        .date-box {
            display: inline-block;
            border: 1px solid #000;
            padding: 3px 14px;
            font-size: 17px;
            font-weight: bold;
            line-height: 1.25;
        }

        /* شريط الموقع / المندوب / العميل / القسم */
        .info-strip {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #000;
            background: #eee;
            font-size: 16px;
            font-weight: bold;
            line-height: 1.25;
            text-align: center;
            padding: 4px 6px;
            margin-top: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .price-table,
        .price-table th,
        .price-table td {
            border: 1px solid #000;
        }

        .price-table th,
        .price-table td {
            padding: 3px 6px;
            line-height: 1.25;
            font-size: 14px;
            text-align: center;
        }

        /* صف العناوين */
        .price-table thead th,
        .price-table tr.column_head th {
            font-size: 16px;
            font-weight: bold;
        }

        /* صف اسم الشركة */
        .price-table td.company_name {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        /* بيانات المنتج */
        .price-table td.num,
        .price-table td.exp,
        .price-table td.unit,
        .price-table td.product-price {
            font-weight: bold;
            text-align: center;
        }

        .price-table td.product-name,
        .price-table td.product-generic {
            font-size: 14px;
            font-weight: bold;
            text-align: left !important;
        }

        .price-table td.product-price {
            font-size: 15px;
        }

        .price-table td.qty-cell {
            text-align: center;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;

                @bottom-center {
                    content: "صفحة " counter(page) " من " counter(pages);
                    font-size: 14px;
                    font-weight: bold;
                }
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

            .price-table tr.column_head,
            .price-table tr.company_row {
                page-break-after: avoid;
                break-after: avoid;
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

            #print table {
                margin-bottom: 8px;
            }

            #print .header {
                min-height: 0;
                margin-bottom: 6px;
            }

            #print .report-header td {
                padding: 2px 6px !important;
            }

            #print .report-header img.logo {
                max-width: 110px;
                max-height: 110px;
            }

            #print .header h1 {
                font-size: 30px !important;
            }

            #print .date-wrap {
                margin: 4px 0 !important;
            }

            #print .date-box {
                font-size: 17px !important;
                padding: 3px 14px !important;
            }

            #print .info-strip {
                font-size: 16px !important;
                padding: 3px 6px !important;
                margin-top: 3px !important;
            }

            #print .price-table th,
            #print .price-table td {
                padding: 3px 6px !important;
                line-height: 1.25 !important;
            }

            #print .price-table thead th,
            #print .price-table tr.column_head th {
                font-size: 16px !important;
                font-weight: bold !important;
            }

            #print .price-table td {
                font-size: 14px !important;
            }

            #print .price-table td.product-name,
            #print .price-table td.product-generic {
                font-size: 14px !important;
                font-weight: bold !important;
            }

            #print .price-table td.product-price {
                font-size: 15px !important;
            }

            #print .price-table td.company_name {
                font-size: 14px !important;
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
                            <td class="header-left">
                                <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" class="logo" alt="الشعار">
                            </td>

                            <td class="header-center">
                                <h1>{{ GeneralSiteSettings('name') }}</h1>
                            </td>

                            <td class="header-right">
                                <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" class="logo" alt="الشعار">
                            </td>
                        </tr>
                    </table>

                    <div class="info-strip">{{ GeneralSiteSettings('location') }}</div>

                    <div class="date-wrap">
                        <span class="date-box">{{ date('d/m/Y') }}</span>
                    </div>

                    @if ($representative)
                        <div class="info-strip">
                            {{ $representative->tree4_name }}{{ $representative->phone ? ' ' . $representative->phone : '' }}
                        </div>
                    @endif

                    @if (!empty($customer_name))
                        <div class="info-strip">العميل: {{ $customer_name }}</div>
                    @endif

                    @if ($selectedCategory)
                        <div class="info-strip">{{ $selectedCategory->name }}</div>
                    @endif
                </div>

                <!-- ✅ الجدول -->
                @php
                    $colspan = $show_expiry ? 7 : 6;
                    $widths = $show_expiry ? [5, 30, 26, 11, 10, 10, 8] : [5, 30, 37, 10, 10, 8];
                @endphp
                <table class="price-table">
                    <colgroup>
                        @foreach ($widths as $width)
                            <col style="width: {{ $width }}%">
                        @endforeach
                    </colgroup>
                    @unless ($show_company)
                        <thead>
                            <tr class="column_head">
                                <th>No.</th>
                                <th colspan="2">Item Name</th>
                                @if ($show_expiry)
                                    <th>Exp</th>
                                @endif
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                    @endunless
                    <tbody>
                        @php $index = 1; @endphp
                        @foreach ($categories as $category)
                            @if ($show_company)
                                <tr class="company_row">
                                    <td colspan="{{ $colspan }}" class="company_name">{{ $category->name }}</td>
                                </tr>
                                <tr class="column_head">
                                    <th>No.</th>
                                    <th colspan="2">Item Name</th>
                                    @if ($show_expiry)
                                        <th>Exp</th>
                                    @endif
                                    <th>Unit</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                </tr>
                            @endif
                            @foreach ($category->products as $product)
                                @php $unit = $product->units->sortByDesc('id')->first(); @endphp
                                <tr>
                                    <td class="num">{{ $index++ }}</td>
                                    <td class="product-name">{{ $product->name }}</td>
                                    <td class="product-generic">{{ $product->name_en }}</td>
                                    @if ($show_expiry)
                                        <td class="exp">
                                            {{ $product->expire_date ? \Illuminate\Support\Carbon::parse($product->expire_date)->format('m/Y') : '' }}
                                        </td>
                                    @endif
                                    <td class="unit">{{ $unit?->name ?? '---' }}</td>
                                    <td class="product-price">
                                        {{ $unit ? number_format($unit->pivot->price) : '---' }}
                                    </td>
                                    <td class="qty-cell"></td>
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
