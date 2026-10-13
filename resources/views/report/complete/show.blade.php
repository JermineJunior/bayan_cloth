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
                <h4 class="content-title mb-0 my-auto">التقارير المالية</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    التقرير الشامل
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

                            <h1 class="invoice-title"> التقرير الشامل :
                                <span></span>
                                <div class="col-md">
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    <p class="invoice-info-row" style="font-size: 14px"><span>تاريخ البداية :</span>
                                        {{ $start }}<span></span></p>
                                    <p class="invoice-info-row" style="font-size: 14px"><span>تاريخ النهاية :</span>
                                        {{ $end }}<span></span></p>
                                    <p class="invoice-info-row" style="font-size: 14px"><span>العملة :</span>
                                        {{ $curencey->name }} ({{ $curencey->symbol }})<span></span></p>
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

                        <!-- المبيعات -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="content-title mb-3">مبيعات</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">عدد الفواتير</th>
                                                    <th style="font-size:14px">الاجمالي</th>
                                                    <th style="font-size:14px">الخصم</th>
                                                    <th style="font-size:14px">مدفوع نقدا</th>
                                                    <th style="font-size:14px">مدفوع بنك</th>
                                                    <th style="font-size:14px">اجل</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $sales->rows_count ?? 0 }}</td>
                                                    <td>{{ number_format(($sales->total_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($sales->discount_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($sales->cash_amount_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($sales->bank_amount_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($sales->deferend_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- مردود المبيعات -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="content-title mb-3">مردود مبيعات</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">عدد الفواتير</th>
                                                    <th style="font-size:14px">الاجمالي</th>
                                                    <th style="font-size:14px">الخصم</th>
                                                    <th style="font-size:14px">مدفوع نقدا</th>
                                                    <th style="font-size:14px">مدفوع بنك</th>
                                                    <th style="font-size:14px">اجل</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $saleReturns->rows_count ?? 0 }}</td>
                                                    <td>{{ number_format(($saleReturns->total_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($saleReturns->discount_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($saleReturns->cash_amount_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($saleReturns->bank_amount_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($saleReturns->deferend_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- المشتريات -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="content-title mb-3">مشتريات</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">عدد الفواتير</th>
                                                    <th style="font-size:14px">الاجمالي</th>
                                                    <th style="font-size:14px">الخصم</th>
                                                    <th style="font-size:14px">مدفوع نقدا</th>
                                                    <th style="font-size:14px">مدفوع بنك</th>
                                                    <th style="font-size:14px">اجل</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $purchases->rows_count ?? 0 }}</td>
                                                    <td>{{ number_format(($purchases->total_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($purchases->discount_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($purchases->cash_amount_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($purchases->bank_amount_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($purchases->deferend_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- مردود المشتريات -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="content-title mb-3">مردود مشتريات</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">عدد الفواتير</th>
                                                    <th style="font-size:14px">الاجمالي</th>
                                                    <th style="font-size:14px">الخصم</th>
                                                    <th style="font-size:14px">مدفوع نقدا</th>
                                                    <th style="font-size:14px">مدفوع بنك</th>
                                                    <th style="font-size:14px">اجل</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $purchaseReturns->rows_count ?? 0 }}</td>
                                                    <td>{{ number_format(($purchaseReturns->total_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($purchaseReturns->discount_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($purchaseReturns->cash_amount_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($purchaseReturns->bank_amount_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($purchaseReturns->deferend_amount ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الايرادات -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="content-title mb-3">الايرادات</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">العدد</th>
                                                    <th style="font-size:14px">نقدا</th>
                                                    <th style="font-size:14px">بنك</th>
                                                    <th style="font-size:14px">الاجمالي</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $revenues->rows_count ?? 0 }}</td>
                                                    <td>{{ number_format(($revenues->cach_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($revenues->bank_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format((($revenues->cach_total ?? 0) + ($revenues->bank_total ?? 0)) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- المصروفات -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="content-title mb-3">المصروفات</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">العدد</th>
                                                    <th style="font-size:14px">نقدا</th>
                                                    <th style="font-size:14px">بنك</th>
                                                    <th style="font-size:14px">الاجمالي</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $expenses->rows_count ?? 0 }}</td>
                                                    <td>{{ number_format(($expenses->cach_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format(($expenses->bank_total ?? 0) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format((($expenses->cach_total ?? 0) + ($expenses->bank_total ?? 0)) / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الملخص -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="content-title mb-3">الملخص</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">صافي المبيعات</th>
                                                    <th style="font-size:14px">صافي المشتريات</th>
                                                    <th style="font-size:14px">الايرادات</th>
                                                    <th style="font-size:14px">المصروفات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ number_format($netSales / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format($netPurchases / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format($revenueTotal / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format($expenseTotal / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
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
