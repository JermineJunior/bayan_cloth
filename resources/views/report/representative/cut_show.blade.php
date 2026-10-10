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
                <h4 class="content-title mb-0 my-auto">تقارير المبيعات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    تقرير عمولة المندوب حسب الفترة
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

                            <h1 class="invoice-title"> تقرير عمولة المندوب حسب الفترة :
                                {{ $representative->tree4_name ?? null }} <span></span>
                                <div class="col-md">
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    <p class="invoice-info-row" style="font-size: 14px"><span>تاريخ البداية :</span>
                                        {{ $start }}<span></span></p>
                                    <p class="invoice-info-row" style="font-size: 14px"><span>تاريخ النهاية :</span>
                                        {{ $end }}<span></span></p>
                                </div>
                            </h1>
                            <div class="billed-from">
                                @if (GeneralSiteSettings('logo'))
                                    <div class="col"> <img class="img-bill" style="width: 240px; height: 240px;"
                                            src="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" /> </div>
                                @endif
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
                                    <h4 class="mg-b-10">الفواتير</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">متسلسل</th>
                                                    <th style="font-size:14px"> التاريخ</th>
                                                    <th style="font-size:14px"> الفاتورة</th>
                                                    <th style="font-size:14px"> العميل</th>
                                                    <th style="font-size:14px"> المبلغ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sales as $sale)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $sale->date }}</td>
                                                        <td>{{ $sale->invoice_number }}</td>
                                                        <td>{{ $sale->customer->tree4_name ?? null }}</td>
                                                        <td>{{ number_format($sale->total_amount / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="4" class="text-center">اجمالي المبيعات</td>
                                                    <td>{{ number_format($sales_total / $curencey->amount, 2) }}
                                                        {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <table class="table table-striped mg-b-0 text-md-nowrap mt-3">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">متسلسل</th>
                                                    <th style="font-size:14px"> التاريخ</th>
                                                    <th style="font-size:14px"> الفاتورة</th>
                                                    <th style="font-size:14px"> العميل</th>
                                                    <th style="font-size:14px"> المبلغ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($returns as $return)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $return->date }}</td>
                                                        <td>{{ $return->invoice_number }}</td>
                                                        <td>{{ $return->customer->tree4_name ?? null }}</td>
                                                        <td>{{ number_format($return->total_amount / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="4" class="text-center">اجمالي مردود المبيعات</td>
                                                    <td>{{ number_format($returns_total / $curencey->amount, 2) }}
                                                        {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div class="alert alert-primary mt-3" role="alert" style="text-align: center">
                                            <h5>صافي الفواتير :
                                                {{ number_format($net_total / $curencey->amount, 2) }}
                                                {{ $curencey->symbol }}</h5>
                                        </div>
                                    </div><!-- bd -->
                                </div><!-- bd -->
                            </div><!-- bd -->
                        </div>

                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mg-b-10">العمولات</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">متسلسل</th>
                                                    <th style="font-size:14px"> التاريخ</th>
                                                    <th style="font-size:14px"> الفاتورة</th>
                                                    <th style="font-size:14px"> المبلغ</th>
                                                    <th style="font-size:14px"> نسبة المندوب %</th>
                                                    <th style="font-size:14px"> عمولة المندوب</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sales as $sale)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $sale->date }}</td>
                                                        <td>{{ $sale->invoice_number }}</td>
                                                        <td>{{ number_format($sale->total_amount / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ $sale->rep_percentage }}</td>
                                                        <td>{{ number_format($sale->rep_cut_amount / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="5" class="text-center">اجمالي عمولة المبيعات</td>
                                                    <td>{{ number_format($sales_cut / $curencey->amount, 2) }}
                                                        {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <table class="table table-striped mg-b-0 text-md-nowrap mt-3">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">متسلسل</th>
                                                    <th style="font-size:14px"> التاريخ</th>
                                                    <th style="font-size:14px"> الفاتورة</th>
                                                    <th style="font-size:14px"> المبلغ</th>
                                                    <th style="font-size:14px"> نسبة المندوب %</th>
                                                    <th style="font-size:14px"> عمولة المندوب</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($returns as $return)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $return->date }}</td>
                                                        <td>{{ $return->invoice_number }}</td>
                                                        <td>{{ number_format($return->total_amount / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ $return->rep_percentage }}</td>
                                                        <td>-{{ number_format($return->rep_cut_amount / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="5" class="text-center">اجمالي عمولة مردود المبيعات</td>
                                                    <td>-{{ number_format($returns_cut / $curencey->amount, 2) }}
                                                        {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div class="alert alert-success mt-3" role="alert" style="text-align: center">
                                            <h5>صافي العمولة :
                                                {{ number_format($net_cut / $curencey->amount, 2) }}
                                                {{ $curencey->symbol }}</h5>
                                        </div>
                                    </div><!-- bd -->
                                </div><!-- bd -->
                            </div><!-- bd -->
                        </div>

                        <button class="btn btn-primary float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                            <i class="mdi mdi-printer ml-1"></i> طبـاعة </button>
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
