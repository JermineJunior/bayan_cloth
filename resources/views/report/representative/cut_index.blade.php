@extends('layouts.app')
@section('css')
    <!--- Internal Select2 css-->
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto">تقارير المندوبين</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    تقرير عمولة المندوب
                </span>
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
                        <h5 class="content-title mb-0 my-auto">تقرير عمولة المندوب :</h5>
                        <a href="javascript:history.back()" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            @if (!$repCutEnabled)
                                <div class="alert alert-warning">
                                    عمولة المندوب غير مفعلة لهذا الفرع، ستظهر قيم العمولة بصفر.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('report.representative.cut.show') }}" id="demo-form2"
                                class="form-horizontal form-label-left">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label>المندوب</label>
                                        <select name="representative_id" class="form-control select2">
                                            @unless ($restrictedRepresentativeId)
                                                <option value="">كل المناديب</option>
                                            @endunless
                                            @foreach ($representatives as $representative)
                                                <option value="{{ $representative->id }}">{{ $representative->tree4_name }}</option>
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
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="inputName" class="control-label">تاريخ
                                            البداية
                                        </label>
                                        <input type="date" name="start" value="{{ date('Y-m-d') }}"
                                            class="form-control" required>
                                    </div>
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="inputName" class="control-label">تاريخ
                                            النهاية
                                        </label>
                                        <input type="date" name="end" value="{{ date('Y-m-d') }}"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <br>

                                @include('report.partials.quick_date_filters')
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary">بـحـث</button>
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
        $('select').select2();
    </script>
@endsection
