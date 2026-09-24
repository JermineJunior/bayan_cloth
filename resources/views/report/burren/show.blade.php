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
                <h4 class="content-title mb-0 my-auto">تقارير المخازن</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    تقرير الجرد
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

                            <h1 class="invoice-title"> تقرير الجرد
                                {{ !$stock ? ' لكل المخازن' : $stock->name }}
                                <span></span>
                                <div class="col-md">
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
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
                            <div class="card mb-3">
                                <div class="card-body">
                                    <form action="{{ route('report.burren.search') }}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-10">
                                                <input type="text" name="search" class="form-control"
                                                    placeholder="ابحث باسم المنتج" value="{{ request('search') }}">
                                            </div>
                                            <input type="hidden" name="stock_id" value="{{ $stock->id ?? null }}">
                                            <input type="hidden" name="curencey_id" value="{{ $curencey->id }}">

                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    بحث
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <td>اسم المنتج</td>
                                                    <td>الرصيد الافتتاحي </td>
                                                    <td>الوارد</td>
                                                    <td>الصادر </td>
                                                    <td>المتوفر </td>
                                                    <td>الوحدة </td>
                                                    <td>سعر الشراء</td>
                                                    <td>سعر البيع</td>
                                                    <td>السعر اجمالي الشراء </td>
                                                    <td>السعر اجمالي البيع </td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total = 0;
                                                @endphp
                                                @foreach ($burrens as $burren)
                                                    <tr>
                                                        <td>{{ $burren->product->name }}</td>
                                                        <td>{{ $burren->opening_balance }}</td>
                                                        <td>{{ $burren->wared }}</td>
                                                        <td>{{ $burren->sader }}</td>
                                                        <td>{{ $burren->available_balance }}</td>
                                                        <td>{{ $burren->product->units->first()->name }}</td>
                                                        <td>{{ number_format($burren->product->units->first()->pivot->price_purchase / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}
                                                        </td>
                                                        <td>{{ number_format($burren->product->units->first()->pivot->price / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}
                                                        </td>
                                                        <td>{{ number_format(($burren->product->units->first()->pivot->price_purchase * $burren->available_balance) / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}
                                                        </td>
                                                        <td>{{ number_format(($burren->product->units->first()->pivot->price * $burren->available_balance) / $curencey->amount, 2) }}
                                                            {{ $curencey->symbol }}
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $total +=
                                                            $burren->product->units->first()->pivot->price_purchase *
                                                            $burren->available_balance;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-center">الاجمالي</td>
                                                    <td>{{ number_format($burrens->sum('opening_balance')) }}</td>
                                                    <td>{{ number_format($burrens->sum('wared')) }}</td>
                                                    <td>{{ number_format($burrens->sum('sader')) }}</td>
                                                    <td>{{ number_format($burrens->sum('available_balance')) }}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{{ number_format($total / $curencey->amount, 2) }}
                                                        {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div><!-- bd -->

                                </div><!-- bd -->
                            </div><!-- bd -->
                        </div>
                        {{-- <div class="row">
                            <div class="col">
                                <div class="alert alert-primary" role="alert" style="text-align: center">
                                    <h4>مرحبـــاً بـك</h4>
                                    <span></span>
                                </div>
                            </div>
                        </div> --}}
                        {{-- //footer the page invoices --}}

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
