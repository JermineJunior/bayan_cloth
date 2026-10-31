@extends('layouts.app')
@section('css')
    <style>
        @media print {
            #print_Button {
                display: none !important;
            }

            @page {
                size: landscape;
                margin: 8mm;
            }

            .table-responsive {
                overflow-x: visible !important;
                overflow: visible !important;
                width: 100% !important;
                display: block !important;
            }

            #print table {
                width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
            }

            #print table th,
            #print table td {
                font-size: 9px !important;
                padding: 2px !important;
                white-space: normal !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
            }

            .text-md-nowrap,
            .text-md-nowrap th,
            .text-md-nowrap td {
                white-space: normal !important;
            }

            body, html {
                width: 100% !important;
                overflow: visible !important;
            }
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">التقارير المالية</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير مديونيات العملاء</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php
        $amount = ($curencey->amount ?? 1) ?: 1;
        $symbol = $curencey->symbol ?? '';
        $fmt = fn($value) => number_format((float) $value / $amount, 2, '.', ',');
    @endphp

    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
            <div class="main-content-body-invoice" id="print">
                <div class="card card-invoice">
                    <div class="card-body">
                        <div class="invoice-header">
                            <h1 class="invoice-title"> تقرير مديونيات العملاء :
                                {{ $customer->tree4_name ?? 'كل العملاء' }}
                                <div class="col-md">
                                    <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                    <p class="invoice-info-row" style="font-size: 14px"><span>حتى تاريخ :</span>
                                        {{ $asOf }}<span></span></p>
                                    <p class="invoice-info-row" style="font-size: 14px"><span>الفرع :</span>
                                        {{ $branch === 'all' ? 'كل الفروع' : 'الفرع الحالي' }}<span></span></p>
                                    <p class="invoice-info-row" style="font-size: 14px"><span>العملة :</span>
                                        {{ $curencey->name ?? '' }}<span></span></p>
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
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">متسلسل</th>
                                                    <th style="font-size:14px">اسم العميل</th>
                                                    <th style="font-size:14px">الهاتف</th>
                                                    <th style="font-size:14px">الرصيد</th>
                                                    <th style="font-size:14px">إجمالي متبقي الفواتير</th>
                                                    <th style="font-size:14px">غير مطابق</th>
                                                    <th style="font-size:14px">عدد الفواتير</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($rows as $row)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $row['customer']->tree4_name }}</td>
                                                        <td>{{ $row['customer']->phone }}</td>
                                                        <td>
                                                            @if ($row['balance'] > 0.005)
                                                                <span class="text-danger">{{ $fmt($row['balance']) }}
                                                                    {{ $symbol }}</span>
                                                                <small>(عليه)</small>
                                                            @elseif ($row['balance'] < -0.005)
                                                                <span class="text-danger">{{ $fmt(abs($row['balance'])) }}
                                                                    {{ $symbol }}</span>
                                                                <small class="text-danger">(له)</small>
                                                            @else
                                                                {{ $fmt(0) }} {{ $symbol }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $fmt($row['invoices_remaining']) }} {{ $symbol }}</td>
                                                        <td>
                                                            @if (abs($row['unmatched']) > 0.01)
                                                                <span class="text-warning">{{ $fmt($row['unmatched']) }}
                                                                    {{ $symbol }}</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $row['invoices']->count() }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">لا توجد مديونيات</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-center">الإجمالي</td>
                                                    <td>{{ $fmt($totals['balance']) }} {{ $symbol }}</td>
                                                    <td>{{ $fmt($totals['invoices_remaining']) }} {{ $symbol }}</td>
                                                    <td>{{ $fmt($totals['unmatched']) }} {{ $symbol }}</td>
                                                    <td>{{ $rows->sum(fn($row) => $row['invoices']->count()) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <span>إجمالي المديونيات (عليه):
                                                            {{ $fmt($totals['debit']) }} {{ $symbol }}</span>
                                                        &nbsp;|&nbsp;
                                                        <span class="text-danger">إجمالي الأرصدة الدائنة (له):
                                                            {{ $fmt($totals['credit']) }} {{ $symbol }}</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($showDetails)
                            @php($invoicesCount = $rows->sum(fn($row) => $row['invoices']->count()))
                            <div class="col-xl-12">
                                <div class="card mg-b-20">
                                    <div class="card-body">
                                        <h5 class="mb-3">تفاصيل الفواتير غير المسددة</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped mg-b-0 text-md-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th style="font-size:14px">متسلسل</th>
                                                        <th style="font-size:14px">اسم العميل</th>
                                                        <th style="font-size:14px">رقم الفاتورة</th>
                                                        <th style="font-size:14px">التاريخ</th>
                                                        @if ($branch === 'all')
                                                            <th style="font-size:14px">الفرع</th>
                                                        @endif
                                                        <th style="font-size:14px">إجمالي الفاتورة</th>
                                                        <th style="font-size:14px">المدفوع</th>
                                                        <th style="font-size:14px">المتبقي</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($invoicesCount > 0)
                                                        @php($count = 0)
                                                        @foreach ($rows as $row)
                                                            @foreach ($row['invoices'] as $invoice)
                                                                <tr>
                                                                    <td>{{ ++$count }}</td>
                                                                    <td>{{ $row['customer']->tree4_name }}</td>
                                                                    <td>{{ $invoice->display_number ?: $invoice->invoice_number }}
                                                                    </td>
                                                                    <td>{{ $invoice->date }}</td>
                                                                    @if ($branch === 'all')
                                                                        <td>{{ $invoice->branch->name ?? '' }}</td>
                                                                    @endif
                                                                    <td>{{ $fmt($invoice->total_amount) }} {{ $symbol }}
                                                                    </td>
                                                                    <td>{{ $fmt((float) $invoice->total_amount - (float) $invoice->deferend_amount) }}
                                                                        {{ $symbol }}</td>
                                                                    <td>{{ $fmt($invoice->deferend_amount) }}
                                                                        {{ $symbol }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="{{ $branch === 'all' ? 8 : 7 }}"
                                                                class="text-center">لا توجد فواتير غير مسددة</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="{{ $branch === 'all' ? 6 : 5 }}"
                                                            class="text-center">إجمالي متبقي الفواتير</td>
                                                        <td>{{ $fmt($totals['invoices_remaining']) }} {{ $symbol }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

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
