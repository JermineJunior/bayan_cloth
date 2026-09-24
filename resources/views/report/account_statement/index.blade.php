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
                    <div class="d-flex justify-content-between gap-2">
                        <h5 class="content-title mb-0 my-auto"> تقرير كشف حساب :</h5>

                        <a href="javascript:history.back()" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            <div class="col-12 text-center">
                                <button class="btn btn-outline-info filter-btn" data-type="customers">عملاء</button>
                                <button class="btn btn-outline-success filter-btn" data-type="suppliers">موردين</button>
                                <button class="btn btn-outline-warning filter-btn" data-type="cash">نقدية</button>
                                <button class="btn btn-outline-primary filter-btn" data-type="others">اخرى</button>

                            </div>
                            <form method="POST" action="{{ route('account.statement.show') }}" id="demo-form2"
                                class="form-horizontal form-label-left">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <span class="tx-danger">*</span><label> الحساب
                                        </label>
                                        <select name="tree4" class="form-control select2">
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>العملة</label>
                                        <select name="curencey_id" id="curencey_id" class="form-control" required>
                                            <option value="">قم باختيار العملة</option>
                                            @foreach ($curenceies as $curencey)
                                                <option value="{{ $curencey->id }}">{{ $curencey->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <br>
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="inputName" class="control-label"> تاريخ
                                            البداية
                                        </label>
                                        <input type="date" name="start" value="<?php echo date('Y-m-d'); ?>"
                                            class="form-control">
                                    </div>
                                    </br>
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="inputName" class="control-label"> تاريخ
                                            النهاية
                                        </label>
                                        <input type="date" name="end" value="<?php echo date('Y-m-d'); ?>"
                                            class="form-control">
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
        // دالة لإعادة تحميل select2 مع الفلترة
        function loadAccounts(type = 'all') {
            $('.select2').empty(); // نظف الخيارات
            $('.select2').select2({
                placeholder: "-- من فضلك اختر حساب --",
                ajax: {
                    url: "{{ route('tree4.filter') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // البحث
                            type: type // نوع الفلترة
                        };
                    },
                    processResults: function(data) {
                        // أضف أول عنصر ثابت
                        let results = [{
                            id: 'عام',
                            text: 'كشف حساب عام'
                        }];

                        // أضف بقية العناصر من السيرفر
                        results = results.concat(
                            data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.tree4_name
                                };
                            })
                        );

                        return {
                            results: results
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });
        }


        $(document).ready(function() {

            // أول ما الصفحة تفتح → يجيب الكل
            loadAccounts();

            // عند الضغط على الأزرار
            $('.filter-btn').on('click', function() {
                let type = $(this).data('type');
                loadAccounts(type);
            });

        })
    </script>
@endsection
