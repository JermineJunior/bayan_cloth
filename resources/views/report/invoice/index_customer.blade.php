@extends('layouts.app')

@section('css')
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto">تقرير
                    @if ($type == 1)
                        المبيعات
                    @elseif ($type == 2)
                        المشتريات
                    @elseif ($type == 3)
                        مردود مبيعات
                    @else
                        مردود مشتريات
                    @endif
                </h5>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    تقرير
                    @if ($type == 1)
                        المبيعات
                    @elseif ($type == 2)
                        المشتريات
                    @elseif ($type == 3)
                        مردود مبيعات
                    @else
                        مردود مشتريات
                    @endif
                    على حسب العميل خلال فترة معينة
                </span>
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
                    <div class="d-flex justify-content-between gap-2">
                        <h5 class="content-title mb-0 my-auto"> تقرير
                            @if ($type == 1)
                                المبيعات
                            @elseif ($type == 2)
                                المشتريات
                            @elseif ($type == 3)
                                مردود مبيعات
                            @else
                                مردود مشتريات
                            @endif
                            على حسب {{ $type == 1 || $type == 3 ? 'العميل' : 'المورد' }} خلال فترة معينة :
                        </h5>

                        <a href="javascript:history.back()" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            @if ($type == 1)
                                <form method="POST" action="{{ route('report.sale_invoice.customer.show') }}"
                                    id="demo-form2" class="form-horizontal form-label-left">
                                @elseif ($type == 2)
                                    <form method="POST" action="{{ route('report.purchase_invoice.supplier.show') }}"
                                        id="demo-form2" class="form-horizontal form-label-left">
                                    @elseif ($type == 3)
                                        <form method="POST"
                                            action="{{ route('report.sale_return_invoice.customer.show') }}" id="demo-form2"
                                            class="form-horizontal form-label-left">
                                        @else
                                            <form method="POST"
                                                action="{{ route('report.purchase_return_invoice.supplier.show') }}"
                                                id="demo-form2" class="form-horizontal form-label-left">
                            @endif
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <select name="customer_id" class="form-control" required>
                                        @if ($type == 1 || $type == 3)
                                            <option value="">-- قم باختيار عميل --</option>
                                        @else
                                            <option value="">-- قم باختيار مورد --</option>
                                        @endif
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->tree4_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select name="curencey_id" id="curencey_id" class="form-control" required>
                                        @foreach ($curenceies as $curencey)
                                            <option value="{{ $curencey->id }}" {{ $curencey->is_base ? 'selected' : '' }}>{{ $curencey->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <span class="tx-danger">*</span><label for="inputName" class="control-label"> تاريخ
                                        البداية
                                    </label>
                                    <input type="date" name="start" value="<?php echo date('Y-m-d'); ?>" class="form-control">
                                </div>
                                </br>
                                <div class="col-6">
                                    <span class="tx-danger">*</span><label for="inputName" class="control-label"> تاريخ
                                        النهاية
                                    </label>
                                    <input type="date" name="end" value="<?php echo date('Y-m-d'); ?>" class="form-control">
                                </div>
                            </div>
                            </br>

                            @include('report.partials.quick_date_filters')
                                <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary"> بـحـث</button>
                            </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--/div-->
        <!-- row closed -->
    </div>
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $('select').select2();
    </script>
@endsection
