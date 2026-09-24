@extends('layouts.app')
@section('css')
    <!--- Internal Select2 css-->
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    {{-- notify css --}}
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
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
                    تقرير حركة المخازن</span>
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
                    
                    @if (session()->has('error_created'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " {{ session('error_created') }}",
                                    type: "error"
                                })
                            }
                        </script>
                    @endif

                    @if (session()->has('success_created'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم حفط البيانات بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif

                    @if (session()->has('success_updated'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم تحديث البيانات بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif
                    @if (session()->has('success_deleted'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم حذف البيانات بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif

                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between gap-2">
                            <h5 class="content-title mb-0 my-auto"> تقرير حركة المخزن :</h5>
    
                            <a href="javascript:history.back()" class="btn btn-warning">
                                <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                            </a>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('burren.info') }}" id="demo-form2"
                        class="form-horizontal form-label-left">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <select name="stock_id" class="form-control">
                                    <option value="">كل المخازن</option>
                                    @foreach ($stocks as $stock)
                                        <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <select name="product_id" class="form-control">
                                    <option value="">كل المنتجات</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
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

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary"> بـحـث</button>
                        </div>
                    </form>
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
    {{-- notify js --}}
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>

    <script>
        $('select').select2();
    </script>
@endsection
