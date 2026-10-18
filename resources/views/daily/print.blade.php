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
                <h5 class="content-title mb-0 my-auto"> ادارة القيود</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
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
                    <h2 style="text-align: center; margin-bottom: 30px;">إيصال قيد</h2>

                    <div class="table-responsive">

                        <table style="width: 100%; border-collapse: collapse; font-size: 18px;">
                            <tr>
                                <td><strong>رقم الإيصال:</strong></td>
                                <td>#{{ $restrictions->first()->constant_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>التاريخ:</strong></td>
                                <td>{{ $restrictions->first()->date }}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>الحساب المدين:</strong></td>
                            </tr>
                            @foreach ($restrictions as $restriction)
                                @if ($restriction->madin == 0)
                                    @continue
                                @endif
                                <tr>
                                    <td></td>
                                    <td>{{ $restriction->tree4->tree4_name }}</td>
                                    <td>{{ $restriction->line_note ?? '-' }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2"><strong>الحساب الدائن:</strong></td>
                            </tr>
                            @foreach ($restrictions as $restriction)
                                @if ($restriction->dain == 0)
                                    @continue
                                @endif
                                <tr>
                                    <td></td>
                                    <td>{{ $restriction->tree4->tree4_name }}</td>
                                    <td>{{ $restriction->line_note ?? '-' }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td><strong>المبلغ:</strong></td>
                                <td>{{ number_format($restrictions->sum('madin') ?? $restrictions->sum('dain'), 2) }} جنيه</td>
                            </tr>

                            <tr>
                                <td><strong>البيان:</strong></td>
                                <td>{{ $restrictions->first()->node ?? '-' }}</td>
                            </tr>
                        </table>

                        <hr style="margin: 40px 0;">

                        <div class="d-flex justify-content-between">
                            <p><strong>التوقيع:</strong> ____________________________</p>
                            <button class="btn btn-primary float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                                <i class="mdi mdi-printer ml-1"></i> طبـاعة </button>
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
            location.reload();
        }
    </script>
@endsection
