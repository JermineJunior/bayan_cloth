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
            position: relative;
            margin-bottom: 40px;
            min-height: 140px;
            /* 🔹 زودنا الارتفاع شوية */
            margin-left: 5px;
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

        .top-right {
            position: absolute;
            top: 0;
            right: 0;
            text-align: right;
            font-size: 14px;
            line-height: 1.6;
        }

        .top-left {
            position: absolute;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            font-size: 14px;
            line-height: 1.4;
            width: 150px;
            min-height: 120px;
        }

        .top-left img.logo {
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

        table,
        th,
        td {
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
            #print_Button {
                display: none !important;
            }

            .top-left {
                display: flex !important;
            }

            .top-left img.logo {
                max-width: 80px !important;
                height: auto;
                display: block !important;
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
                    <!-- الشمال (شعار + بريد وهاتف) -->
                    <div class="top-left">
                        <div>
                            <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" class="logo" alt="الشعار">
                        </div>
                        @if (GeneralSiteSettings('email'))
                            <div class="company-info">{{ GeneralSiteSettings('email') }}</div>
                        @endif
                        @if (GeneralSiteSettings('phone'))
                            <div class="company-info">{{ GeneralSiteSettings('phone') }}</div>
                        @endif

                    </div>

                    <!-- العنوان الرئيسي -->
                    <h1>{{ GeneralSiteSettings('name') }}</h1>


                    <!-- اليمين (تاريخ مثلاً) -->
                    <div class="top-right">
                        <p>{{ date('d/m/Y') }}</p>
                        @if ($representative)
                            <p class="rep-info">اسم المندوب : {{ $representative->tree4_name }}</p>
                            @if ($representative->phone)
                                <p class="rep-info">رقم تلفون المندوب : {{ $representative->phone }}</p>
                            @endif
                        @endif
                        <p style=" direction: rtl;">اسم العميل: {{ '..........................' }}</p>
                    </div>
                </div>

                <!-- ✅ الجدول -->
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Generic name</th>
                            <th>Expare date</th>
                            <th>Unit</th>
                            <th>W.Price</th>
                            <th>Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $index = 1; @endphp
                        @foreach ($categories as $category)
                            <td colspan="2" class="company_name">{{ $category->name }}</td>
                            @foreach ($category->products as $product)
                                <tr>
                                    <td>{{ $index++ }}</td>
                                    <td class="product-name">{{ $product->name }}</td>
                                    <td class="product-generic">{{ $product->name_en }}</td>
                                    <td>{{ $product->expire_date }}</td>
                                    <td>{{ $product->units()->orderByDesc('id')->first()->name }}</td>
                                    <td class="product-price">
                                        {{ number_format($product->units()->orderByDesc('id')->first()->pivot->price) }}
                                    </td>
                                    <td></td>
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
            var printContents = document.getElementById('print').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
@endsection
