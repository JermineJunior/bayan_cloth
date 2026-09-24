@extends('layouts.app')
@section('css')
    <style>
        @media print {
            #print_Button {
                display: none !important;
            }
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">ادارة المخازن</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير
                    الجرد
                </span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
            <div class=" main-content-body-invoice" id="print">
                <div class="card card-invoice">
                    <div class="card-body">
                        <div class="invoice-header">

                            <h1 class="invoice-title"> تقرير الحركة
                                {{ $stock_id == null ? ' لكل المخازن' : $burrens[0]->stock->name  }} {{ $product_id == null? ' لكل المنتجات': ' لل'.$burrens[0]->product->name }} 
                                <div class="col-md">
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    {{-- <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p> --}}
                                    <p class="invoice-burren-row" style="font-size: 14px"><span>تاريخ البداية :</span>
                                        {{ $start }}<span></span></p>
                                    <p class="invoice-burren-row" style="font-size: 14px"><span>تاريخ النهاية :</span>
                                        {{ $end }}<span></span></p>
                                </div>
                            </h1>
                            <div class="billed-from">
                                <div class="col"> <img class="img-bill" style="width: 240px; height: 240px;"
                                        src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" /> </div>
                                <br />
                                <h5>{{ GeneralSiteSettings('name') }}</h5>
                                <p>
                                    هاتف : {{ GeneralSiteSettings('phone') }}</br>
                                    البريد الإلكتروني:{{ GeneralSiteSettings('email') }}</p>
                            </div><!-- billed-from -->
                        </div><!-- invoice-header -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">متسلسل</th>
                                                    <th style="font-size:14px">التاريخ</th>
                                                    <th style="font-size:14px">نوع العملية</th>
                                                    <th style="font-size:14px">رقم الفاتورة</th>
                                                    <th style="font-size:14px">الجهة</th>
                                                    @if ($stock_id == null)
                                                        <th style="font-size:14px">المخزن</th>
                                                    @endif
                                                    <th style="font-size:14px">الوارد</th>
                                                    <th style="font-size:14px">الصادر</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($burrens as $burren)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $burren->created_at->format('Y-m-d') }}</td>
                                                        @if ($burren->type == 1)
                                                            <td>مبيعات</td>
                                                        @elseif ($burren->type == 2)
                                                            <td>مشتريات</td>
                                                        @elseif ($burren->type == 3)
                                                            <td>مردود مبيعات</td>
                                                        @elseif ($burren->type == 4)
                                                            <td>مردود مشتريات</td>
                                                        @elseif ($burren->type == 6)
                                                            <td>تحويل بين المخازن</td>
                                                        @else
                                                            <td>ارصدة افتتاحية</td>
                                                        @endif
                                                        @if ($burren->type == 1)
                                                            <td>{{ $burren->sale_invoice->invoice_number }}</td>
                                                        @elseif ($burren->type == 2)
                                                            <td>{{ $burren->purchase_invoice->invoice_number }}</td>
                                                        @elseif ($burren->type == 3)
                                                            <td>{{ $burren->sale_invoice_return->invoice_number }}</td>
                                                        @elseif ($burren->type == 4)
                                                            <td>{{ $burren->purchase_invoice_return->invoice_number }}</td>
                                                        @else
                                                            <td> -</td>
                                                        @endif
                                                        <td>{{ $burren->product->name }}</td>
                                                        @if ($stock_id == null)
                                                            <td>{{ $burren->stock->name }}</td>
                                                        @endif
                                                        <td>{{ $burren->wared ?? 0 }}</td>
                                                        <td>{{ $burren->sader ?? 0 }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="6" class="text-center">الاجمالي</td>
                                                    <td>{{ number_format($burrens->sum('wared')) }}</td>
                                                    <td>{{ number_format($burrens->sum('sader')) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><!-- bd -->

                                </div><!-- bd -->
                            </div><!-- bd -->
                        </div>

                        <div class="row">
                            <div class="col">
                                <h6 style="text-align: center">
                                    <span> {{ GeneralSiteSettings('location') }}
                                        - البريد الإلكتروني : {{ GeneralSiteSettings('email') }}
                                        - هاتف : {{ GeneralSiteSettings('phone') }}
                                    </span>

                                    </br>
                                </h6>
                            </div>
                        </div>
                        {{-- //footer the page invoices --}}
                        <hr class="mg-b-40">
                        {{-- <a class="btn btn-purple float-left mt-3 mr-2" href="">
                        <i class="mdi mdi-currency-usd ml-1"></i>Pay Now
                    </a> --}}
                        <button class="btn btn-primary float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                            <i class="mdi mdi-printer ml-1"></i> طبـاعة </button>
                        {{-- <a href="#" class="btn btn-success float-left mt-3">
                        <i class="mdi mdi-telegram ml-1"></i>Send Invoice
                    </a> --}}
                    </div>
                </div>
            </div>
        </div><!-- COL-END -->
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!--Internal  Chart.bundle js -->
    <script src="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>
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
