@extends('layouts.app')

@section('css')
    <style>
        .card {
            border: none !important;
        }

        .invoice {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
        }

        .invoice-header {
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 7px solid #042d7a;
        }

        .invoice-header img {
            width: 100px;
        }

        .invoice-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .modern-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 15px;
            direction: rtl;
        }

        /* العنوان */
        .modern-details .detail-box:first-child {
            grid-column: 1 / -1;
            background: #042d7a;
            color: #fff;
            font-size: 20px;
            font-weight: bold;
            padding: 12px;
        }

        /* البوكسات العادية */
        .detail-box {
            background: #f9fbff;
            border: 1px solid #e3e8f3;
            border-radius: 10px;
            padding: 10px 12px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .detail-box .label {
            display: block;
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 3px;
        }

        .detail-box .value {
            font-size: 16px;
            font-weight: bold;
            color: #042d7a;
        }

        /* نخلي العنوان أبيض */
        .modern-details .detail-box:first-child .value {
            color: #fff;
        }


        .table-container {
            position: relative;
        }

        .table-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('image/logo/17307014763895.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.06;
            z-index: 1;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            background-color: transparent;
            position: relative;
            z-index: 2;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .invoice-table tfoot {
            background: #f9f9f9;
        }

        .invoice-table tfoot th,
        .invoice-table tfoot td {
            border: none;
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        /* .invoice-footer {
                        margin-top: 20px;
                        font-size: 14px;
                        direction: rtl;
                        text-align: right;
                    } */

        /* .footer-signature {
                                        display: flex;
                                        justify-content: space-between;
                                    }

                                    .signature {
                                        margin-right: 15em;
                                    } */

        @media print {
            #print_Button {
                display: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
            <div class=" main-content-body-invoice" id="print">
                <div class="card card-invoice">
                    <div class="card-body">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="invoice">
                                        <div class="invoice-header">
                                            @if (GeneralSiteSettings('logo'))
                                                <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}"
                                                    alt="Logo">
                                            @endif
                                            <div class="text-center">
                                                <h1>{{ GeneralSiteSettings('name') }}</h1>
                                                <h6>{{ GeneralSiteSettings('location') }}</h6>
                                                <h6>{{ GeneralSiteSettings('phone') }}</h6>
                                            </div>
                                            @if (GeneralSiteSettings('logo'))
                                                <img src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}"
                                                    alt="Logo">
                                            @endif
                                        </div>

                                        <div class="invoice-details modern-details">
                                            <div class="detail-box">
                                                <span class="value">طلب شراء من مورد</span>
                                            </div>

                                            <div class="detail-box">
                                                <span class="label">تاريخ الفاتورة</span>
                                                <span class="value">{{ $purchase_order->date }}</span>
                                            </div>

                                            <div class="detail-box">
                                                <span class="label">المورد</span>
                                                <span
                                                    class="value">{{ $purchase_order->supplier->tree4_name ?? '---' }}</span>
                                            </div>
                                        </div>


                                        <div class="table-container">
                                            <table class="invoice-table">
                                                <thead>
                                                    <tr>
                                                        <th>م</th>
                                                        <th class="w-30p">المنتج</th>
                                                        <th>الكمية</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($purchase_order->details as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td class="wd-30p">{{ $item->product->name }}</td>
                                                            <td>{{ number_format($item->qty) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div><!-- bd -->
                            </div><!-- bd -->
                        </div>
                        <hr class="mg-b-40">
                        <button class="btn btn-primary float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                            <i class="mdi mdi-printer ml-1"></i> طبـاعة
                        </button>
                    </div>
                </div>
            </div>
        </div><!-- COL-END -->
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
            window.location.href = '{{ route('purchase_order.index') }}';
        }
    </script>
@endsection
