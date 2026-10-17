@extends('layouts.app')
@section('css')
    <!--- Internal Select2 css-->
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-label {
            font-weight: 500;
            font-size: 16px;
            margin-left: 4px;
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> تقارير المخازن </h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    تقرير مقصل للمخازن حسب الفترة</span>
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
                        <h5 class="content-title mb-0 my-auto"> تقرير مفصل للمخازن حسب الفترة :</h5>

                        <a href="javascript:history.back()" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="card-body">
                        <form method="POST" action="{{ route('report.stock.details.show') }}" id="demo-form2"
                            class="form-horizontal form-label-left">
                            @csrf

                            <div class="row">
                                <div class="col-md-12">
                                    <span class="tx-danger">*</span><label> اسم المخزن
                                    </label>
                                    <select required name="stock_id" class="form-control select2">
                                        @foreach ($stocks as $stock)
                                            <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <span class="tx-danger">*</span><label for="inputName" class="control-label"> تاريخ
                                        البداية
                                    </label>
                                    <input type="date" name="start" value="<?php echo date('Y-m-d'); ?>" class="form-control"
                                        required>
                                </div>
                                </br>
                                <div class="col">
                                    <span class="tx-danger">*</span><label for="inputName" class="control-label"> تاريخ
                                        النهاية
                                    </label>
                                    <input type="date" name="end" value="<?php echo date('Y-m-d'); ?>" class="form-control"
                                        required>
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
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!--Internal  Form-elements js-->
    <script src="{{ URL::asset('assets/js/advanced-form-elements.js') }}"></script>
    <!-- Internal form-elements js -->
    <script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>

    <script>
        $('select').select2();
    </script>
@endsection
