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
                <h5 class="content-title mb-0 my-auto">الحسابات</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير
                    الدفاتر المحاسبية
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
                            <h1 class="invoice-title">تقرير الدفاتر المحاسبية <span></span>
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
                                {{-- <h5> {{GeneralSiteSettings('location')}} </h5< /br> --}}
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

                                                    <th style="font-size:14px">الحساب </th>
                                                    <th style="font-size:14px">مدين </th>
                                                    <th style="font-size:14px">دائن </th>
                                                    <th style="font-size:14px">نوع العملية </th>
                                                    <th style="font-size:14px">رقم العملية</th>
                                                    <th style="font-size:14px">التاريخ</th>
                                                    <th style="font-size:14px">البيان</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($one as $operation)
                                                    <tr>
                                                        <td>{{ $operation->tree4->tree4_name }}</td>
                                                        <td>{{ number_format($operation->madin / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($operation->dain / $curencey->amount, 2) }} {{ $curencey->symbol }}</td>
                                                        @if ($operation->type == 1)
                                                            <td> مبيعات</td>
                                                        @elseif($operation->type == 2)
                                                            <td> مشتريات </td>
                                                        @elseif($operation->type == 3)
                                                            <td> مردودات مبيعات </td>
                                                        @elseif($operation->type == 4)
                                                            <td>مردودات مشتريات </td>
                                                        @elseif($operation->type == 5)
                                                            <td>قيد عام </td>
                                                        @elseif($operation->type == 6)
                                                            <td>قيد مركب </td>
                                                        @elseif($operation->type == 7)
                                                            <td> رصيد افتتاحي </td>
                                                        @elseif($operation->type == 8)
                                                            <td> ايرادات </td>
                                                        @elseif($operation->type == 9)
                                                            <td>مصروفات</td>
@elseif($operation->type == 10)
<td>منصرفات مشتريات خارجية</td>                                                        @endif
                                                        <td>{{ $operation->constant_number }}</td>
                                                        <td>{{ $operation->date }}</td>
                                                        <td>{{ $operation->node }}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div><!-- bd -->

                                </div><!-- bd -->
                            </div><!-- bd -->
                        </div>
                        <div class="col-xl-12">
                            <div class="card mg-b-20">
                                <div class="card-body">
                                    <span></span>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">إجمالي المدين</th>
                                                    <th style="font-size:14px">إجمالي الدائن</th>
                                                    <th style="font-size:14px"> الرصيد</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ number_format($Madin / $curencey->amount, 2) }} {{ $curencey->symbol }} </td>
                                                    <td style="color:green">{{ number_format($Dain / $curencey->amount, 2) }} {{ $curencey->symbol }} </td>
                                                    <td>{{ number_format(($Madin - $Dain) / $curencey->amount, 2) }} {{ $curencey->symbol }} </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </br>
                        {{-- <div class="row">
                            <div class="col">
                                <div class="alert alert-primary" role="alert" style="text-align: center">
                                    <h4>مرحبـــاً بـك</h4>
                                    <span></span>
                                </div>
                            </div>
                        </div> --}}
                        {{-- //footer the page invoices --}}

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
