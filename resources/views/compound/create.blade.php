@extends('layouts.app')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
    <!--Internal  treeview -->
    <link href="{{ URL::asset('assets/plugins/treeview/treeview-rtl.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> إدارة النظام المحاسبي </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0"> / القيود المركبة</span>
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
                    @if (session()->has('warning'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: "لم تتم عملية الادخال بالشكل الصحيح ",
                                    type: "warning"
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
                            <form id="compoundForm" action="{{ route('compound.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        <label for="constant_number" class="control-label"> رقم القيد</label>
                                        <input type="text" id="constant_number" value="{{ $constant_number }}" readonly
                                            name="constant_number" class="form-control">
                                    </div>
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="date" class="control-label"> تاريخ
                                            القيد</label>
                                        <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>"
                                            class="form-control" placeholder="يرجى ادخال التاريخ">
                                    </div>
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="note" class="control-label"> بيان
                                            القيد</label>
                                        <input type="text" id="note" name="note" class="form-control"
                                            placeholder="يرجى ادخال بيان القيد">
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6">
                                        <span class="tx-danger">*</span><label for="dain">الحساب الدائن</label>
                                        <select name="dain" id="dain" class="form-control select2-from"></select>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <span class="tx-danger">*</span><label for="price_dain" class="control-label">
                                            المبلغ</label>
                                        <input id="price_dain" type="number" name="price_dain" class="form-control"
                                            onkeyup="checkPrice()" placeholder="يرجى ادخال المبلغ">
                                    </div>
                                </div>

                                <div class="row" id="row-madin">
                                    <div class="col-6">
                                        <span class="tx-danger">*</span><label>الحساب المدين</label>
                                        <select name="madin[1]" class="form-control select2-to"></select>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <span class="tx-danger">*</span><label class="control-label">
                                            المبلغ</label>
                                        <input type="number" name="price_madin[1]" class="form-control price_madin"
                                            onkeyup="checkPrice()" placeholder="يرحى ادخال المبلغ">
                                    </div>

                                    <div class="col-6 mb-3">
                                        <select name="madin[2]" class="form-control select2-to"></select>
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="price_madin[2]" class="form-control price_madin"
                                            onkeyup="checkPrice()" placeholder="يرجي ادخال  المبلغ">
                                    </div>

                                    <div class="col-12 mt-3">
                                        <button type="button" id="add-madin-row" class="btn btn-secondary mb-3">إضافة
                                            حساب دائن</button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" id="saveButton" class="btn btn-primary"> إضــافة
                                        القيد</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>
    <script>
        document.getElementById('add-madin-row').addEventListener('click', function() {
            const bodyAccount = document.getElementById('row-madin');
            let indexOf = bodyAccount.querySelectorAll('.select2-to').length + 1;
            const row = `
                        <div class="row">
                            <div class="col-6">
                                <select name="madin[${indexOf}]" id="madin_${indexOf}" class="form-control select2-to"></select>
                            </div>
                            <div class="col-6">
                                <input type="number" name="price_madin[${indexOf}]" id="price_madin_${indexOf}" class="form-control price_madin"
                                    onkeyup="checkPrice()"  placeholder="يرجي ادخال  المبلغ">
                            </div>
                        </div>
                        <br />`;

            document.getElementById('add-madin-row').insertAdjacentHTML('beforebegin', row);
            $(`#madin_${indexOf}`).rules(
                'add', {
                    required: true,
                    messages: {
                        required: "يرجى اختيار الحساب المدين"
                    }
                });

            $(`#price_madin_${indexOf}`).rules(
                'add', {
                    required: true,
                    messages: {
                        required: 'يرجى ادخال مبلغ الحساب المدين'
                    }
                });
            $('.select2-to').select2({
                ajax: {
                    url: "{{ route('select2.tree4') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // نص البحث
                            exclude: getExcludedAccounts() // استبعاد الحسابات المختارة
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true,
                },
                placeholder: 'يرجى اختيار الحساب إلى',
            });


        });
        $('#compoundForm').validate({
            rules: {
                dain: {
                    required: true,
                },
                "madin[1]": {
                    required: true,
                },
                "madin[2]": {
                    required: true,
                },
                price_dain: {
                    required: true,
                    number: true
                },
                "price_madin[1]": {
                    required: true,
                    number: true
                },
                "price_madin[2]": {
                    required: true,
                    number: true
                },
                date: {
                    required: true,
                    date: true,
                },
                note: {
                    required: true
                },
                constant_number: {
                    number: true,
                },
            },
            messages: {
                dain: {
                    required: "يرجى إدخال الحساب الدائن.",
                },
                "madin[1]": {
                    required: "يرجى إدخال الحساب المدين.",
                },
                "madin[2]": {
                    required: "يرجى إدخال الحساب المدين.",
                },
                price_dain: {
                    required: "يرجى إدخال مبلغ الحساب الدائن.",
                    number: "يجب أن يكون المبلغ عدداً."
                },
                "price_madin[1]": {
                    required: "يرجى إدخال مبلغ الحساب المدين.",
                    number: "يجب أن يكون المبلغ عدداً."
                },
                "price_madin[2]": {
                    required: "يرجى إدخال مبلغ الحساب المدين.",
                    number: "يجب أن يكون المبلغ عدداً."
                },
                date: {
                    required: "يرجى إدخال التاريخ.",
                    date: "يرجى إدخال تاريخ صحيح."

                },
                note: {
                    required: "يرجى إدخال بيان القيد.",
                },
                constant_number: {
                    required: "يجب ادخال رقم القيد.",
                },
                total: {
                    required: "يرجى إدخال الاجمالي للفاتورة.",
                    number: "يجب أن يكون الاجمالي للفاتورة عدداً.",
                    min: "يجب أن يكون الاجمالي اكبر من الصفر."
                }
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

        $('.select2-from').select2({
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
            placeholder: 'يرجى اختيار الحساب من',
        });

        // تهيئة الحقول "إلى حساب" (المدين)
        $('.select2-to').select2({
            ajax: {
                url: "{{ route('select2.tree4') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // نص البحث
                        exclude: getExcludedAccounts() // استبعاد الحسابات المختارة
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true,
            },
            placeholder: 'يرجى اختيار الحساب إلى',
        });

        // تحديث الحقول "إلى حساب" عند تغيير الحقل "من حساب"
        $('.select2-from').on('change', function() {
            $('.select2-to').val(null).trigger('change'); // إعادة تعيين الحقول "إلى حساب"
            $('.select2-to').prop('disabled', false);
        });

        // تحديث الحقول "إلى حساب" عند تغيير أي حقل "إلى حساب"
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

        function checkPrice() {
            let price_dain_value = document.getElementById('price_dain').value || 0;
            let price_madin = document.querySelectorAll('.price_madin');
            let sum = 0;
            price_madin.forEach(function(madin) {
                let price_madin_value = madin.value || 0;
                sum += parseFloat(price_madin_value);

                console.log(price_madin_value, price_dain_value, sum);


            });

            document.getElementById('saveButton').disabled = (parseFloat(price_dain_value) !== parseFloat(sum));

        }
    </script>
@endsection
