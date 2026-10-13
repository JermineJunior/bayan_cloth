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
                <h4 class="content-title mb-0 my-auto">تقارير المبيعات</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير العملاء الأكثر مبيعا
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

                            <h1 class="invoice-title"> تقرير العملاء الأكثر مبيعا :
                                <span></span>
                                <div class="col-md">
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    @if ($start)
                                        <p class="invoice-info-row" style="font-size: 14px"><span>تاريخ البداية :</span>
                                            {{ $start }}<span></span></p>
                                    @endif
                                    @if ($end)
                                        <p class="invoice-info-row" style="font-size: 14px"><span>تاريخ النهاية :</span>
                                            {{ $end }}<span></span></p>
                                    @endif
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
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">#</th>
                                                    <th style="font-size:14px">العميل</th>
                                                    <th style="font-size:14px">عدد الفواتير</th>
                                                    <th style="font-size:14px">اجمالي المبيعات</th>
                                                    <th style="font-size:14px">المرتجعات</th>
                                                    <th style="font-size:14px">الصافي</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($customers as $customer)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $customer->customer->tree4_name ?? '-' }}</td>
                                                        <td>{{ $customer->invoices_count }}</td>
                                                        <td>{{ number_format($customer->total_sales / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($customer->total_returns / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($customer->net_sales / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">لا توجد بيانات</td>
                                                    </tr>
                                                @endforelse
                                                <tr>
                                                    <td colspan="2" class="text-center">الاجمالي</td>
                                                    <td>{{ $customers->sum('invoices_count') }}</td>
                                                    <td>{{ number_format($customers->sum('total_sales') / $curencey->amount, 2) }}
                                                        {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format($customers->sum('total_returns') / $curencey->amount, 2) }}
                                                        {{ $curencey->symbol }}</td>
                                                    <td>{{ number_format($customers->sum('net_sales') / $curencey->amount, 2) }}
                                                        {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
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
