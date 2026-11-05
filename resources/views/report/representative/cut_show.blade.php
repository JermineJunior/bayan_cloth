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
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">تقارير المندوبين</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    تقرير عمولة المندوب
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php($amount = $curencey->amount ?: 1)

    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
            <div class="main-content-body-invoice" id="print">
                <div class="card card-invoice">
                    <div class="card-body">
                        <div class="invoice-header">
                            <h1 class="invoice-title">
                                تقرير عمولة المندوب :
                                {{ $representative->tree4_name ?? 'كل المناديب' }}
                                <div class="col-md">
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
                            </div>
                        </div>

                        @if (!$repCutEnabled)
                            <div class="alert alert-warning">
                                عمولة المندوب غير مفعلة لهذا الفرع، تم إظهار قيم العمولة بصفر.
                            </div>
                        @endif

                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="font-size:14px">متسلسل</th>
                                                    <th style="font-size:14px">المندوب</th>
                                                    <th style="font-size:14px">إجمالي الفواتير</th>
                                                    <th style="font-size:14px">إجمالي المرتجعات</th>
                                                    <th style="font-size:14px">عمولة المبيعات</th>
                                                    <th style="font-size:14px">عمولة المرتجعات</th>
                                                    <th style="font-size:14px">صافي العمولة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($rows as $row)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $row->representative_name }}</td>
                                                        <td>{{ number_format($row->invoices_total / $amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($row->returns_total / $amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($row->sales_cut / $amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($row->returns_cut / $amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($row->net_cut / $amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">لا توجد بيانات في هذه الفترة</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            @if ($rows->isNotEmpty())
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="2" class="text-center"><strong>الإجمالي</strong></td>
                                                        <td>{{ number_format($rows->sum('invoices_total') / $amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($rows->sum('returns_total') / $amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($rows->sum('sales_cut') / $amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($rows->sum('returns_cut') / $amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                        <td>{{ number_format($rows->sum('net_cut') / $amount, 2) }}
                                                            {{ $curencey->symbol }}</td>
                                                    </tr>
                                                </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($representative)
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="content-title mb-0 my-auto">فواتير المبيعات</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped mg-b-0 text-md-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th style="font-size:14px">متسلسل</th>
                                                        <th style="font-size:14px">رقم الفاتورة</th>
                                                        <th style="font-size:14px">التاريخ</th>
                                                        <th style="font-size:14px">العميل</th>
                                                        <th style="font-size:14px">الإجمالي</th>
                                                        <th style="font-size:14px">العمولة</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($invoices as $invoice)
                                                        <tr>
                                                            <th scope="row">{{ $loop->iteration }}</th>
                                                            <td>{{ $invoice->invoice_number }}</td>
                                                            <td>{{ $invoice->date }}</td>
                                                            <td>{{ $invoice->customer->tree4_name ?? null }}</td>
                                                            <td>{{ number_format($invoice->total_amount / $amount, 2) }}
                                                                {{ $curencey->symbol }}</td>
                                                            <td>{{ number_format(($repCutEnabled ? $invoice->rep_cut_amount : 0) / $amount, 2) }}
                                                                {{ $curencey->symbol }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">لا توجد فواتير في هذه الفترة
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                @if ($invoices->isNotEmpty())
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="4" class="text-center"><strong>الإجمالي</strong>
                                                            </td>
                                                            <td>{{ number_format($invoices->sum('total_amount') / $amount, 2) }}
                                                                {{ $curencey->symbol }}</td>
                                                            <td>{{ number_format(($repCutEnabled ? $invoices->sum('rep_cut_amount') : 0) / $amount, 2) }}
                                                                {{ $curencey->symbol }}</td>
                                                        </tr>
                                                    </tfoot>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="content-title mb-0 my-auto">مرتجعات المبيعات</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped mg-b-0 text-md-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th style="font-size:14px">متسلسل</th>
                                                        <th style="font-size:14px">رقم الفاتورة</th>
                                                        <th style="font-size:14px">التاريخ</th>
                                                        <th style="font-size:14px">العميل</th>
                                                        <th style="font-size:14px">الإجمالي</th>
                                                        <th style="font-size:14px">العمولة</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($invoiceReturns as $invoiceReturn)
                                                        <tr>
                                                            <th scope="row">{{ $loop->iteration }}</th>
                                                            <td>{{ $invoiceReturn->invoice_number }}</td>
                                                            <td>{{ $invoiceReturn->date }}</td>
                                                            <td>{{ $invoiceReturn->customer->tree4_name ?? null }}</td>
                                                            <td>{{ number_format($invoiceReturn->total_amount / $amount, 2) }}
                                                                {{ $curencey->symbol }}</td>
                                                            <td>{{ number_format(($repCutEnabled ? $invoiceReturn->rep_cut_amount : 0) / $amount, 2) }}
                                                                {{ $curencey->symbol }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">لا توجد مرتجعات في هذه
                                                                الفترة</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                @if ($invoiceReturns->isNotEmpty())
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="4" class="text-center"><strong>الإجمالي</strong>
                                                            </td>
                                                            <td>{{ number_format($invoiceReturns->sum('total_amount') / $amount, 2) }}
                                                                {{ $curencey->symbol }}</td>
                                                            <td>{{ number_format(($repCutEnabled ? $invoiceReturns->sum('rep_cut_amount') : 0) / $amount, 2) }}
                                                                {{ $curencey->symbol }}</td>
                                                        </tr>
                                                    </tfoot>
                                                @endif
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
