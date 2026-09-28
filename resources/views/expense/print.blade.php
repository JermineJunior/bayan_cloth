@extends('layouts.app')

@section('css')
    <style>
        @media print {

            #print_Button,
            .create_expense {
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
                <h5 class="content-title mb-0 my-auto"> ادارة المصروفات</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
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
            <div class="card" id="print">
                <div class="card-body">
                    <h2 style="text-align: center; margin-bottom: 30px;">إيصال صرف</h2>

                    <div class="table-responsive">

                        <table style="width: 100%; border-collapse: collapse; font-size: 18px;">
                            <tr>
                                <td><strong>رقم الإيصال:</strong></td>
                                <td>#{{ $expense->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>التاريخ:</strong></td>
                                <td>{{ $expense->date }}</td>
                            </tr>
                            <tr>
                                <td><strong>اسم الحساب:</strong></td>
                                <td>{{ $expense->madin->tree4_name }}</td>
                            </tr>
                            @if ($expense->cach_id != '')
                                <tr>
                                    <td><strong>اسم الخزنة:</strong></td>
                                    <td>{{ $expense->cach->tree4_name }}</td>
                                </tr>
                            @endif
                            @if ($expense->bank_id != '')
                                <tr>
                                    <td><strong>اسم البنك:</strong></td>
                                    <td>{{ $expense->bankName->tree4_name }}</td>
                                </tr>
                            @endif
                            @if ($expense->transaction_no)
                                <tr>
                                    <td><strong>رقم عملية البنك:</strong></td>
                                    <td>{{ $expense->transaction_no }}</td>
                                </tr>
                            @endif

                            <tr>
                                <td><strong>المبلغ:</strong></td>
                                <td>{{ number_format($expense->cach_name + $expense->bank_name, 2) }} جنيه</td>
                            </tr>

                            <tr>
                                <td><strong>البيان:</strong></td>
                                <td>{{ $expense->note ?? '-' }}</td>
                            </tr>
                        </table>

                        <hr style="margin: 40px 0;">

                        <div class="d-flex justify-content-between">
                            <p><strong>التوقيع:</strong> ____________________________</p>
                            <div>
                                <button class="btn btn-primary float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                                    <i class="mdi mdi-printer ml-1"></i> طبـاعة </button>
                                <a href="{{ route('expense.index') }}"
                                    class="btn btn-outline-primary float-left mt-3 mr-2 create_expense">
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

@section('js')
    <script>
        function printDiv() {
            var printContents = document.getElementById('print').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.href = '{{ route('expense.index') }}';
        }
    </script>
@endsection
