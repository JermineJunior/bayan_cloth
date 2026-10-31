@extends('layouts.app')
@section('css')
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
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> التقارير المالية </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير مديونيات العملاء</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between gap-2">
                        <h5 class="content-title mb-0 my-auto"> تقرير مديونيات العملاء :</h5>

                        <a href="javascript:history.back()" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            <form method="GET" action="{{ route('report.customer_debts.show') }}" id="demo-form2"
                                class="form-horizontal form-label-left">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label> العميل </label>
                                        <select name="customer_id" class="form-control select2">
                                            <option value="">كل العملاء</option>
                                            @foreach ($customers as $item)
                                                <option value="{{ $item->id }}">{{ $item->tree4_name }}</option>
                                            @endforeach
                                        </select>
                                        <small>في حالة عدم اختيار عميل سيتم عرض مديونيات كل العملاء</small>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label> حتى تاريخ </label>
                                        <input type="date" name="as_of" value="{{ date('Y-m-d') }}" class="form-control">
                                        <small>اتركه كما هو لاحتساب المديونية حتى تاريخ اليوم</small>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label> الفرع </label>
                                        <select name="branch" class="form-control">
                                            <option value="current" selected>الفرع الحالي</option>
                                            <option value="all">كل الفروع</option>
                                        </select>
                                        <small>العملاء مشتركون بين الفروع</small>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label> العملة </label>
                                        <select name="curencey_id" id="curencey_id" class="form-control" required>
                                            @foreach ($curenceies as $item)
                                                <option value="{{ $item->id }}" {{ $item->is_base ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-check">
                                            <input type="hidden" name="show_invoices" value="0">
                                            <input type="checkbox" name="show_invoices" value="1" id="show_invoices"
                                                class="form-check-input" checked>
                                            <label class="form-check-label" for="show_invoices">عرض تفاصيل الفواتير</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary"> بـحـث</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/advanced-form-elements.js') }}"></script>
    <script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>

    <script>
        $('.select2').select2();
    </script>
@endsection
