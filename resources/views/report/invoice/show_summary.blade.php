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
                <h4 class="content-title mb-0 my-auto">تقارير
                    @if ($type == 1)
                        المبيعات
                    @elseif ($type == 2)
                        المشتريات
                    @elseif ($type == 3)
                        مردود مبيعات
                    @else
                        مردود مشتريات
                    @endif
                </h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير
                    @if ($type == 1)
                        المبيعات
                    @elseif ($type == 2)
                        المشتريات
                    @elseif ($type == 3)
                        مردود مبيعات
                    @else
                        مردود مشتريات
                    @endif
                    ملخص
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

                            <h1 class="invoice-title"> تقرير
                                @if ($type == 1)
                                    المبيعات
                                @elseif ($type == 2)
                                    المشتريات
                                @elseif ($type == 3)
                                    مردود مبيعات
                                @else
                                    مردود مشتريات
                                @endif ملخص <span></span>
                                <div class="col-md">
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    {{-- <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p> --}}
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
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">متسلسل</th>
                                                    <th style="font-size:14px">التاريخ</th>
                                                    <th style="font-size:14px">الفاتورة</th>
                                                    <th style="font-size:14px">
                                                        {{ $type == 1 || $type == 3 ? 'العميل' : 'المورد' }}</th>
                                                    <th style="font-size:14px"> المبلغ</th>
                                                    <th style="font-size:14px">الخصم</th>
                                                    <th style="font-size:14px">صافي الفاتورة</th>
                                                    @if ($type == 1)
                                                        <th style="font-size:14px">ربح للفاتورة</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($operations as $operation)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $operation->date }}</td>
                                                        <td>{{ $operation->invoice_number }}</td>
                                                        <td>{{ $type == 1 || $type == 3 ? $operation->customer->tree4_name ?? 'لا يوجد عميل' : $operation->supplier->tree4_name ?? 'لا يوجد مورد' }}
                                                        </td>
                                                        <td>{{ number_format($operation->tax_amount + $operation->sub_total_amount / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}
                                                        </td>
                                                        <td>{{ number_format($operation->discount_amount / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($operation->total_amount / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        @if ($type == 1)
                                                            <td>{{ number_format($operation->profit / $curencey->amount, 2) }}
                                                                {{ $curencey->symbol }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="6" class="text-center">الاجمالي
                                                    </td>
                                                    <td>{{ number_format($operations->sum('total_amount') / $curencey->amount, 2) }}
                                                        {{ $curencey->symbol }}
                                                    </td>
                                                    @if ($type == 1)
                                                        <td>{{ number_format($operations->sum('profit') / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                    @endif
                                                </tr>
                                                @if ($type == 1)
                                                    <tr>
                                                        <td colspan="6" class="text-center">متوسط الارباح</td>
                                                        <td></td>
                                                        <td>{{ number_format($operations->avg('profit') / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div><!-- bd -->

                                </div><!-- bd -->
                            </div><!-- bd -->
                        </div>


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
