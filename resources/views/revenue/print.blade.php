@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/print-document.css') }}?v={{ filemtime(public_path('assets/css/print-document.css')) }}">
    <style>
        .card {
            border: none !important;
        }

        .invoice {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            background-color: transparent;
            position: relative;
            z-index: 2;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .invoice-table tfoot {
            background: #f9f9f9;
        }

        .invoice-table tfoot th,
        .invoice-table tfoot td {
            font-weight: 600;
        }

        @media print {
            #print_Button,
            .create_revenue {
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
                <h5 class="content-title mb-0 my-auto"> ادارة الايردادات</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    ايصال مالي</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="main-content-body-invoice" id="print">
                <div class="card">
                    <div class="card-body">
                        <div class="invoice">
                            <h2 style="text-align: center; margin-bottom: 30px;">إيصال إيراد</h2>

                            <div class="table-responsive">

                                <table style="width: 100%; border-collapse: collapse; font-size: 18px;">
                                    <tr>
                                        <td><strong>رقم الإيصال:</strong></td>
                                        <td>#{{ $revenue->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>التاريخ:</strong></td>
                                        <td>{{ $revenue->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>اسم الحساب:</strong></td>
                                        <td>{{ $revenue->dain->tree4_name }}</td>
                                    </tr>
                                    @if ($revenue->cach_id != '')
                                        <tr>
                                            <td><strong>اسم الخزنة:</strong></td>
                                            <td>{{ $revenue->cach->tree4_name }}</td>
                                        </tr>
                                    @endif
                                    @if ($revenue->bank_id != '')
                                        <tr>
                                            <td><strong>اسم البنك:</strong></td>
                                            <td>{{ $revenue->bankName->tree4_name }}</td>
                                        </tr>
                                    @endif
                                    @if ($revenue->transaction_no)
                                        <tr>
                                            <td><strong>رقم عملية البنك:</strong></td>
                                            <td>{{ $revenue->transaction_no }}</td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td><strong>المبلغ:</strong></td>
                                        <td>{{ number_format($revenue->cach_name + $revenue->bank_name, 2) }} جنيه</td>
                                    </tr>
                                    <tr>
                                        <td><strong>البيان:</strong></td>
                                        <td>{{ $revenue->note ?? '-' }}</td>
                                    </tr>
                                </table>

                                @if ($revenue->allocations->count() > 0)
                                    <h4 style="margin: 20px 0 10px;">الفواتير المسددة</h4>
                                    <table class="invoice-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>رقم الفاتورة</th>
                                                <th>تاريخ الفاتورة</th>
                                                <th>إجمالي الفاتورة</th>
                                                <th>المبلغ المسدد</th>
                                                <th>المتبقي حاليا</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($revenue->allocations as $index => $allocation)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $allocation->saleInvoice->invoice_number }}</td>
                                                    <td>{{ $allocation->saleInvoice->date }}</td>
                                                    <td>{{ number_format($allocation->saleInvoice->total_amount, 2) }}</td>
                                                    <td>{{ number_format($allocation->amount, 2) }}</td>
                                                    <td>{{ number_format($allocation->saleInvoice->deferend_amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5">إجمالي المبلغ المسدد</th>
                                                <td>{{ number_format($revenue->allocations->sum('amount'), 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                @endif

                                <hr style="margin: 40px 0;">

                                <div class="d-flex justify-content-between">
                                    <p><strong>التوقيع:</strong> ____________________________</p>
                                    <div>
                                        <button class="btn btn-primary float-left mt-3 mr-2" id="print_Button"
                                            onclick="window.print()">
                                            <i class="mdi mdi-printer ml-1"></i> طبـاعة </button>
                                        <a href="{{ route('revenue.index') }}"
                                            class="btn btn-outline-primary float-left mt-3 mr-2 create_revenue">
                                            <i class="mdi mdi-plus ml-1"></i> ايراد جديد
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
