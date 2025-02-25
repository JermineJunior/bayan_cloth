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
                <h5 class="content-title mb-0 my-auto"> التقارير المحاسبية </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير كشف حساب</span>
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
                    <div class="d-flex">
                        <h5 class="content-title mb-0 my-auto"> تقرير كشف حساب :</h5>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            <form method="POST" action="{{ route('account.statement.show') }}" id="demo-form2" class="form-horizontal form-label-left">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        <div class="alert alert-primary" role="alert">
                                            اَختر الحساب مع تحديد الفترة من تاريخ البداية الي تاريخ النهاية لكي يتم عرض
                                            تفاصيل الحساب خلال الفترة المحددة .
                                            <span class="alert-inner--icon"><i class="ti-bell"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <span class="tx-danger">*</span><label> الحساب
                                    </label>
                                    <select required name="tree4" class="form-control select2"></select>
                                </div>
                                <br>
                                <div class="row">
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
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة الحقل "من حساب"
            $('.select2').select2({
                ajax: {
                    url: "{{ route('select2.tree4') }}", // رابط البحث
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // نص البحث
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true,
                },
                placeholder: 'يرجى اختيار الحساب ',
            });
        });
    </script>
@endsection
