@extends('layouts.app')
@section('css')
    <!-- Internal Select2 css -->
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> إدارة النظام المحاسبي </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ القيود اليومية</span>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إضافة قيد</span>
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
                    @if ($errors->any())
                        <div class="alert alert-outline-warning" role="alert">
                            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                <span aria-hidden="true">&times;</span></button>
                            @foreach ($errors->all() as $error)
                                <strong>خطأ!</strong> {{ $error }}
                            @endforeach
                        </div>
                    @endif
                    @if (session()->has('success_created'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم إضافة القيد بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif
                    @if (session()->has('success_updated'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم تحديث القيد بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif
                    @if (session()->has('success_deleted'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم حذف القيد بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif

                    <div class="d-flex">
                        <h5 class="content-title mb-0 my-auto"> إضاف قيد جديد :</h5>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            <form id="dailyForm" action="{{ route('daily.store') }}" method="POST"
                                class="form-horizontal form-label-left">
                                @csrf
                                <div class="col">
                                    {{--  <span class="tx-danger">*</span><label for="inputName" class="control-label"> رقم القيد</label>  --}}
                                    <input required type="hidden" value="{{ $constant_number }}" name="constant_number"
                                        class="form-control" title="يرجي ادخال إسم الحساب">
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <span class="tx-danger">*</span><label for="from_account">الحساب المدين</label>
                                        <select class="select2-from form-control" name="madin">
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <span class="tx-danger">*</span><label for="to_account">الحساب الدائن</label>
                                        <select class="select2-to form-control" name="dain" disabled>
                                        </select>
                                    </div>
                                </div>

                                </br>
                                <div class="row">
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="inputName" class="control-label">
                                            المبلغ</label>
                                        <input  type="number" name="price" class="form-control"
                                            title="يرجي ادخال المبلغ" >
                                    </div>
                                    </br>
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="inputName" class="control-label">
                                            التاريخ </label>
                                        <input  type="date" name="date" value="<?php echo date('Y-m-d'); ?>"
                                            class="form-control" title="يرجي ادخال التاريخ">
                                    </div>
                                </div>
                                </br>
                                <div class="row">
                                    <div class="col">
                                        <span class="tx-danger">*</span><label>البيان</label>
                                        <textarea rows="5" name="node" class="form-control"></textarea>
                                    </div>
                                </div>
                                </br>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary"> إضــافة القيد</button>
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
    <!-- Internal Select2 -->
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>

    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة الحقل "من حساب"
            $('.select2-from').select2({
                ajax: {
                    url: "{{ route('select2.tree4') }}",
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
                placeholder: 'يرجى اختيار الحساب من',
            });

            // تهيئة الحقل "إلى حساب"
            $('.select2-to').select2({
                ajax: {
                    url: "{{ route('select2.tree4') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // نص البحث
                            exclude: getExcludedAccounts() // إرسال القيمة المختارة في "من حساب" كـ exclude
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true,
                },
                placeholder: 'يرجى اختيار الحساب الى',
            });

            // تحديث الحقل "إلى حساب" عند تغيير الحقل "من حساب"
            $('.select2-from').on('change', function() {
                $('.select2-to').val(null).trigger('change'); // إعادة تعيين الحقل "إلى حساب"
                $('.select2-to').prop('disabled', false);

            });

            $('.select2-to').on('change', function() {
                // التأكد من عدم اختيار نفس الحساب في أكثر من حقل
                let selectedAccounts = getSelectedAccounts();
                let duplicate = selectedAccounts.some((account, index) => selectedAccounts.indexOf(
                    account) !== index);

                if (duplicate) {
                    $(this).val(null).trigger('change'); // إعادة تعيين الحقل إذا تم اختيار حساب مكرر
                    notif({
                        msg: "لا يمكن اختيار نفس الحساب أكثر من مرة.",
                        type: "error"
                    });
                }
            });

            $('.select2-to').on('change', function() {
                if ($('.select2-to').val() === $('.select2-from').val()) {
                    $('.select2-to').val(null).trigger('change'); // إعادة تعيين الحقل "إلى حساب"
                    notif({
                        msg: "لا يمكن ادخال حساب الى بنفس الحساب من.",
                        type: "error"
                    })
                }
            });

            $('#dailyForm').validate({
                rules:{
                    madin:{required: true},
                    dain:{required: true},
                    price:{required: true,number:true},
                    date:{required:true}
                },
                messages:{
                    madin:{required: "يرجي اختيار  الحساب المدين"},
                    dain:{required: "يرجي اختيار الحساب الدائن"},
                    price:{required: "يرجي ادخال المبلغ", number: "يرجي ادخال قيمة رقمية"},
                    date:{required: "يرجي ادخال التاريخ"}
                },
                errorPlacement: function(error, element) {
                    if (element.closest('.input-group').length) {
                        error.insertAfter(element.closest('.input-group'));
                    } else if (element.hasClass('select2-hidden-accessible')) {
                        // ✅ التعامل مع `select2`
                        error.insertAfter(element.next('.select2'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                errorElement: "span",
                errorClass: "text-danger",
                highlight: function(element) {
                    $(element).closest('.form-group').addClass('has-error');
                },
                unhighlight: function(element) {
                    $(element).closest('.form-group').removeClass('has-error');
                }
            });

            // دالة لجلب الحسابات المستبعدة
            function getExcludedAccounts() {
                let excludedAccounts = [];

                // إضافة الحساب الدائن إلى القائمة المستبعدة
                let debtorAccount = $('.select2-from').val();
                if (debtorAccount) {
                    excludedAccounts.push(debtorAccount);
                }

                // إضافة الحسابات المدينة المختارة إلى القائمة المستبعدة
                $('.select2-to').each(function() {
                    let account = $(this).val();
                    if (account) {
                        excludedAccounts.push(account);
                    }
                });

                return excludedAccounts;
            }

            // دالة لجلب الحسابات المختارة
            function getSelectedAccounts() {
                let selectedAccounts = [];

                // إضافة الحساب الدائن إلى القائمة المختارة
                let debtorAccount = $('.select2-from').val();
                if (debtorAccount) {
                    selectedAccounts.push(debtorAccount);
                }

                // إضافة الحسابات المدينة المختارة إلى القائمة المختارة
                $('.select2-to').each(function() {
                    let account = $(this).val();
                    if (account) {
                        selectedAccounts.push(account);
                    }
                });

                return selectedAccounts;
            }
        });
    </script>
@endsection
