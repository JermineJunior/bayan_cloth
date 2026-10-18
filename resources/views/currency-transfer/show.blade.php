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
                <h5 class="content-title mb-0 my-auto"> إدارة النظام المحاسبي </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0"> / تحويل العملات</span>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إيصال تحويل</span>
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
                            <h2 style="text-align: center; margin-bottom: 30px;">إيصال تحويل عملة</h2>

                            <div class="table-responsive">

                                <table style="width: 100%; border-collapse: collapse; font-size: 18px;">
                                    <tr>
                                        <td><strong>التاريخ:</strong></td>
                                        <td>{{ $transfer->date }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>من حساب:</strong></td>
                                        <td>{{ $transfer->fromAccount->tree4_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>من عملة:</strong></td>
                                        <td>{{ $transfer->fromCurrency->name }}
                                            ({{ $transfer->fromCurrency->symbol }})</td>
                                    </tr>
                                    <tr>
                                        <td><strong>من مبلغ:</strong></td>
                                        <td>{{ number_format($transfer->from_amount, 2) }}
                                            {{ $transfer->fromCurrency->symbol }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>إلى حساب:</strong></td>
                                        <td>{{ $transfer->toAccount->tree4_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>إلى عملة:</strong></td>
                                        <td>{{ $transfer->toCurrency->name }}
                                            ({{ $transfer->toCurrency->symbol }})</td>
                                    </tr>
                                    <tr>
                                        <td><strong>إلى مبلغ:</strong></td>
                                        <td>{{ number_format($transfer->to_amount, 2) }}
                                            {{ $transfer->toCurrency->symbol }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>القيمة بالعملة الأساسية:</strong></td>
                                        <td>{{ number_format($transfer->base_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>رقم القيد:</strong></td>
                                        <td>{{ $transfer->constant_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ملاحظة:</strong></td>
                                        <td>{{ $transfer->note ?? '-' }}</td>
                                    </tr>
                                </table>

                                <hr style="margin: 40px 0;">

                                <div class="d-flex justify-content-between">
                                    <p><strong>التوقيع:</strong> ____________________________</p>
                                    <div>
                                        <button class="btn btn-primary float-left mt-3 mr-2" id="print_Button"
                                            onclick="window.print()">
                                            <i class="mdi mdi-printer ml-1"></i> طبـاعة </button>
                                        <a href="{{ route('currency-transfer.index') }}"
                                            class="btn btn-outline-primary float-left mt-3 mr-2">
                                            <i class="fa fa-list ml-1"></i> قائمة التحويلات
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
