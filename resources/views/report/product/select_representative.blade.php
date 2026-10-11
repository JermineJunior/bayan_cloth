@extends('layouts.app')
@section('css')
    <!--- Internal Select2 css-->
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> تقرير المخازن </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير كشف السعر</span>
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
                        <h5 class="content-title mb-0 my-auto"> تقرير كشف السعر :</h5>

                        <a href="javascript:history.back()" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            <form method="POST" action="{{ route('product.price') }}" id="demo-form2"
                                class="form-horizontal form-label-left">
                                @csrf

                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                <div class="col">
                                    <label> المندوب</label>
                                    <select  name="representative_id" class="form-control select2">
                                        <option value="">-- من فضلك قم باختيار المندوب --</option>
                                        @foreach ($representatives as $representative)
                                            <option value="{{ $representative->id }}">{{ $representative->tree4_name }}</option>
                                        @endforeach
                                    </select>
                                    <small>في حالة لم يتم اختيار اي مندوب سوف يتم عرض تقرير كشف الاسعار بدون اي بيانات للمندوب</small>
                                </div>
                                <br>
                                <div class="col">
                                    <label> القسم</label>
                                    <select name="category_id" class="form-control select2">
                                        <option value="">-- من فضلك قم باختيار القسم --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br>
                                <div class="col">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="show_expiry" value="1" class="custom-control-input"
                                            id="show_expiry" checked>
                                        <label class="custom-control-label" for="show_expiry">إظهار تاريخ الصلاحية</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="show_company" value="1" class="custom-control-input"
                                            id="show_company" checked>
                                        <label class="custom-control-label" for="show_company">إظهار أسماء الشركات</label>
                                    </div>
                                </div>
                                <br>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" name="mode" value="all" class="btn btn-primary mx-2"> عرض سعر
                                        عام</button>
                                    <button type="submit" name="mode" value="category" class="btn btn-primary mx-2"> عرض
                                        سعر للقسم المحدد</button>
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
