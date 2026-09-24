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
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ كشف
                    حساب مستخدم
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

                            <h1 class="invoice-title"> كشف حساب :
                                {{ $user->name }} <span></span>
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
                                                    <th style="font-size:14px">البيان</th>
                                                    <th style="font-size:14px">السعر</th>
                                                    <th style="font-size:14px">الكمية</th>
                                                    <th style="font-size:14px">البونص</th>
                                                    <th style="font-size:14px">تاريخ الصلاحية</th>
                                                    <th style="font-size:14px"> الوحدة</th>
                                                    <th style="font-size:14px">الجملة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- <span>{{$item->agent->tree4_code}}</span> --}}

                                                {{-- @livewire('model' ,['item_id' => $item->agent->tree4_code]) --}}
                                                @foreach ($operations as $operation)
                                                    @foreach ($operation->details as $item)
                                                        <tr>
                                                            <th scope="row">{{ $loop->iteration }}</th>
                                                            <td>{{ $item->product->name }}</td>
                                                            <td>{{ number_format($item->price / $curencey->amount,2) }} {{ $curencey->symbol }}</td>
                                                            <td>{{ $item->qty }}</td>
                                                            <td>{{ $item->bounce }}</td>
                                                            <td>{{ $item->expire_date }}</td>
                                                            <td>{{ $item->unit->name }}</td>
                                                            <td>{{ number_format($item->row_sub_total / $curencey->amount,2) }} {{ $curencey->symbol }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2" class="text-center pt-5"> جملة الفواتير</td>
                                                    <td class="pt-5">
                                                        {{ number_format(($operations->sum('sub_total_amount') + $operations->sum('tax_amount')) / $curencey->amount,2) }} {{ $curencey->symbol }}
                                                    </td>
                                                    <td colspan="2" class="text-center pt-5">مستلم نقدا</td>
                                                    <td class="pt-5">
                                                        {{ number_format($operations->sum('cash_amount_total') / $curencey->amount,2) }} {{ $curencey->symbol }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-center">جملة خصم الفترة</td>
                                                    <td>{{ number_format($operations->sum('discount_amount') / $curencey->amount,2) }} {{ $curencey->symbol }}</td>
                                                    <td colspan="2" class="text-center">مستلم بالبنك</td>
                                                    <td>{{ number_format($operations->sum('bank_total_amount') / $curencey->amount,2) }} {{ $curencey->symbol }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-center">صافي فواتير الفترة</td>
                                                    <td>{{ number_format($operations->sum('total_amount') / $curencey->amount,2) }} {{ $curencey->symbol }}</td>
                                                    <td colspan="2" class="text-center">اجل </td>
                                                    <td>{{ number_format($operations->sum('deferend_amount') / $curencey->amount,2) }} {{ $curencey->symbol }}</td>
                                                </tr>
                                            </tfoot>
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
