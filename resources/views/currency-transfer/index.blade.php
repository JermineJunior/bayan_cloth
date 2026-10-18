@extends('layouts.app')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet" />
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> إدارة النظام المحاسبي </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0"> / تحويل العملات</span>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة التحويلات</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    @if (session()->has('success_created'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم إضافة التحويل بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif
                    <div class="d-flex justify-content-between gap-2">
                        <h5 class="content-title mb-0 my-auto"> قائمة تحويل العملات :</h5>

                        <div>
                            <a href="{{ route('currency-transfer.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus ml-1"></i> إضافة تحويل
                            </a>
                            <a href="javascript:history.back()" class="btn btn-warning">
                                <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="example1">
                            <thead>
                                <tr>
                                    <th class="wd-5p border-bottom-0">#</th>
                                    <th class="wd-10p border-bottom-0">التاريخ</th>
                                    <th class="wd-15p border-bottom-0">من حساب</th>
                                    <th class="wd-15p border-bottom-0">إلى حساب</th>
                                    <th class="wd-15p border-bottom-0">من</th>
                                    <th class="wd-15p border-bottom-0">إلى</th>
                                    <th class="wd-20p border-bottom-0">ملاحظات</th>
                                    <th class="wd-10p border-bottom-0">العمليات </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transfers as $transfer)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $transfer->date }}</td>
                                        <td>{{ $transfer->fromAccount->tree4_name }}</td>
                                        <td>{{ $transfer->toAccount->tree4_name }}</td>
                                        <td>{{ number_format($transfer->from_amount, 2) }}
                                            {{ $transfer->fromCurrency->symbol }}</td>
                                        <td>{{ number_format($transfer->to_amount, 2) }}
                                            {{ $transfer->toCurrency->symbol }}</td>
                                        <td>{{ $transfer->note }}</td>
                                        <td>
                                            <a class="btn btn-outline-info btn-sm"
                                                href="{{ route('currency-transfer.show', $transfer->id) }}"
                                                title="عرض">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
