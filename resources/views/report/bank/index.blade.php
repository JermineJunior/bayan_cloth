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
                <h5 class="content-title mb-0 my-auto"> التقارير المحاسبية </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير كشف حساب بنك</span>
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
                        <h5 class="content-title mb-0 my-auto"> تقرير كشف حساب بنك :</h5>

                        <a href="javascript:history.back()" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            <form method="POST" action="{{ route('report.bank.show') }}" id="demo-form2"
                                class="form-horizontal form-label-left">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <span class="tx-danger">*</span><label> الحساب
                                        </label>
                                        <select required name="bank_id" class="form-control select2">
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->tree4_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>العملة</label>
                                        <select name="curencey_id" id="curencey_id" class="form-control" required>
                                            @foreach ($curenceies as $curencey)
                                                <option value="{{ $curencey->id }}" {{ $curencey->is_base ? 'selected' : '' }}>{{ $curencey->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <span class="tx-danger">*</span><label> نوع التقرير</label>
                                        <div class="d-flex gap-4 justify-content-start align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type" id="invoices"
                                                    value="1" checked>
                                                <label class="form-check-label  mr-4" for="invoices">
                                                    تقرير الفواتير
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type" id="accounts"
                                                    value="2">
                                                <label class="form-check-label mr-4" for="accounts">
                                                    تقرير المالي
                                                </label>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="inputName" class="control-label"> تاريخ
                                            البداية
                                        </label>
                                        <input type="date" name="start" value="<?php echo date('Y-m-d'); ?>"
                                            class="form-control" required>
                                    </div>
                                    </br>
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="inputName" class="control-label"> تاريخ
                                            النهاية
                                        </label>
                                        <input type="date" name="end" value="<?php echo date('Y-m-d'); ?>"
                                            class="form-control" required>
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
