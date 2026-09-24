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
                <h5 class="content-title mb-0 my-auto"> إدارة فواتير المشتريات </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0"> / منصرفات المشتريات الخارجية</span>
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

                    <div class="d-flex justify-content-between gap-2">
                        <h5 class="content-title mb-0 my-auto"> اضافة منصرفات المشتريات الخارجية :</h5>

                        <a href="javascript:history.back()" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            <form id="compoundForm" action="{{ route('external.purchase.store',$purchase_invoice->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-4">
                                        <label class="control-label"> رقم الفاتورة </label>
                                        <input type="text" disabled class="form-control"
                                            value="{{ $purchase_invoice->invoice_number ?? null }}">
                                    </div>
                                    <div class="col-xl-4">
                                        <label class="control-label"> تاريخ الفاتورة </label>
                                        <input type="text" disabled class="form-control"
                                            value="{{ $purchase_invoice->created_at->toDateString() ?? null }}">
                                    </div>
                                    <div class="col-xl-4">
                                        <label class="control-label"> قيمة الفاتورة </label>
                                        <input type="text" disabled class="form-control"
                                            value="{{ number_format($purchase_invoice->total_amount ?? 0) }}">
                                    </div>
                                </div>
                                <br>
                                <div class="row" id="row-dain">
                                    <div class="col-xl-6">
                                        <span class="tx-danger">*</span><label for="dain">الحساب الدائن</label>
                                        @forelse ($operations as $operation)
                                            @if ($operation->madin == 0)
                                                <select name="dain[]" class="form-control select2-from">
                                                    <option value="">-- من فضلك قم باختيار حساب --</option>
                                                    @foreach ($dain_trees as $tree)
                                                        <option @selected($tree->tree4_code == $operation->tree4_code) value="{{ $tree->id }}">
                                                            {{ $tree->tree4_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <br>
                                                <br>
                                            @endif
                                        @empty
                                            <select name="dain[]" id="dain" class="form-control select2-from">
                                                <option value="">-- من فضلك قم باختيار حساب --</option>
                                                @foreach ($dain_trees as $tree)
                                                    <option value="{{ $tree->id }}">{{ $tree->tree4_name }}</option>
                                                @endforeach
                                            </select>
                                        @endforelse
                                    </div>
                                    <div class="col-xl-5 mb-2">
                                        <span class="tx-danger">*</span><label for="price_dain" class="control-label">
                                            المبلغ</label>
                                        @forelse ($operations as $operation)
                                            @if ($operation->madin == 0)
                                                <input type="text" name="price_dain[]" class="form-control price_dain"
                                                    onkeyup="checkPrice()" placeholder="يرجى ادخال المبلغ"
                                                    value="{{ number_format($operation->dain) }}">
                                                <br>
                                            @endif
                                        @empty
                                            <input id="price_dain" type="text" name="price_dain[]"
                                                class="form-control price_dain" onkeyup="checkPrice()"
                                                placeholder="يرجى ادخال المبلغ">
                                        @endforelse
                                    </div>

                                    <div class="col-xl-1 col-md-1">
                                        <label for="">حذف</label>
                                        @forelse ($operations as $operation)
                                            @if ($operation->madin == 0)
                                                <button type="button" class="btn btn-danger remove-madin"><i
                                                        class="fa fa-trash"></i></button>
                                                <br>
                                                <br>
                                            @endif
                                        @empty
                                            <button type="button" class="btn btn-danger remove-madin"><i
                                                    class="fa fa-trash"></i></button>
                                        @endforelse
                                    </div>
                                </div>
                                <div id="dain-container"></div>

                                <div class="row" id="row-madin">
                                    <div class="col-xl-6">
                                        <span class="tx-danger">*</span><label>الحساب المدين</label>
                                        @forelse ($operations as $operation)
                                            @if ($operation->dain == 0)
                                                <select name="madin[]" class="form-control select2-to">
                                                    <option value="">-- من فضلك قم باختيار حساب --</option>
                                                    @foreach ($madin_trees as $tree)
                                                        <option @selected($operation->tree4_code == $tree->tree4_code) value="{{ $tree->id }}">
                                                            {{ $tree->tree4_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <br><br>
                                            @endif
                                        @empty
                                            <select name="madin[]" class="form-control select2-to">
                                                <option value="">-- من فضلك قم باختيار حساب --</option>
                                                @foreach ($madin_trees as $tree)
                                                    <option value="{{ $tree->id }}">{{ $tree->tree4_name }}</option>
                                                @endforeach
                                            </select>
                                        @endforelse
                                    </div>
                                    <div class="col-xl-5 mb-2">
                                        <span class="tx-danger">*</span><label class="control-label">
                                            المبلغ</label>
                                        @forelse ($operations as $operation)
                                            @if ($operation->dain == 0)
                                                <input type="text" name="price_madin[]"
                                                    class="form-control price_madin" onkeyup="checkPrice()"
                                                    placeholder="يرحى ادخال المبلغ"
                                                    value="{{ number_format($operation->madin) }}">
                                                <br>
                                            @endif
                                        @empty
                                            <input type="text" name="price_madin[]" class="form-control price_madin"
                                                onkeyup="checkPrice()" placeholder="يرحى ادخال المبلغ">
                                        @endforelse
                                    </div>

                                    <div class="col-xl-1 col-md-1">
                                        <label for="">حذف</label>
                                        @forelse ($operations as $operation)
                                            @if ($operation->dain == 0)
                                                <button type="button" class="btn btn-danger remove-madin"><i
                                                        class="fa fa-trash"></i></button>
                                                <br><br>
                                            @endif
                                        @empty
                                            <button type="button" class="btn btn-danger remove-madin"><i
                                                    class="fa fa-trash"></i></button>
                                        @endforelse
                                    </div>
                                </div>
                                <div id="madin-container"></div>

                                <div class="col-12 mt-3">
                                    <button type="button" id="add-dain-row" class="btn btn-secondary mb-3">إضافة
                                        حساب دائن</button>
                                    <button type="button" id="add-madin-row" class="btn btn-info mb-3">إضافة
                                        حساب مدين</button>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" id="saveButton" class="btn btn-primary"> حفظ
                                    </button>
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
        $('.select2-from').select2();
        $('.select2-to').select2();
        document.getElementById('add-madin-row').addEventListener('click', function() {
            const rowMadinContainer = document.getElementById('madin-container');
            let indexOf = rowMadinContainer.querySelectorAll('.select2-to').length + 1;

            const row = `
        <div class="row madin-row">
            <div class="col-xl-6 col-md-12" style="margin-top:15px">
                <select name="madin[${indexOf}]" class="form-control select2-to">
                    <option> -- من فضلك قم باختيار حساب --</option>
                    @foreach ($madin_trees as $tree)
                        <option value="{{ $tree->id }}"> {{ $tree->tree4_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xl-5 col-md-11 my-3">
                <input type="text" name="price_madin[${indexOf}]" class="form-control price_madin"
                    onkeyup="checkPrice()" placeholder="يرجى ادخال المبلغ">
            </div>
            <div class="col-xl-1 col-md-1 mt-3">
                <button type="button" class="btn btn-danger remove-madin"><i class="fa fa-trash"></i></button>
            </div>
        </div>`;

            rowMadinContainer.insertAdjacentHTML('beforeend', row);
            $('.select2-to').select2();

            attachCommaFormatter('.price_madin');

            // تطبيق تنسيق الفاصلة على حقل المبلغ
            attachCommaFormatter($('.price_madin').last().attr('id'));
        });

        document.getElementById('add-dain-row').addEventListener('click', function() {
            const rowDainContainer = document.getElementById('dain-container');
            let indexOf = rowDainContainer.querySelectorAll('.select2-from').length + 1;

            const row = `
                            <div class="row dain-row">
                                <div class="col-xl-6 col-md-12" style="margin-top:15px">
                                    <select name="dain[${indexOf}]" class="form-control select2-from">
                                        <option> -- من فضلك قم باختيار حساب --</option>
                                        @foreach ($dain_trees as $tree)
                                            <option value="{{ $tree->id }}"> {{ $tree->tree4_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-5 col-md-11 my-3">
                                    <input type="text" name="price_dain[${indexOf}]" class="form-control price_dain"
                                        onkeyup="checkPrice()" placeholder="يرجى ادخال المبلغ">
                                </div>
                                <div class="col-xl-1 col-md-1 mt-3">
                                    <button type="button" class="btn btn-danger remove-madin"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>`;


            rowDainContainer.insertAdjacentHTML('beforeend', row);

            $('.select2-from').select2();

            attachCommaFormatter('.price_dain');


            // تطبيق تنسيق الفاصلة على حقل المبلغ
            attachCommaFormatter($('.price_dain').last().attr('id'));
        });

        // الاستماع للنقر على زر الحذف لأي صف مدين (باستخدام التفويض delegation)
        document.getElementById('madin-container').addEventListener('click', function(e) {
            if (e.target.closest('.remove-madin')) {
                const row = e.target.closest('.madin-row');
                if (row) {
                    row.remove();
                }
            }
        });

        // الاستماع للنقر على زر الحذف لأي صف مدين (باستخدام التفويض delegation)
        document.getElementById('dain-container').addEventListener('click', function(e) {
            if (e.target.closest('.remove-dain')) {
                const row = e.target.closest('.dain-row');
                if (row) {
                    row.remove();
                }
            }
        });



        $('#compoundForm').validate({
            rules: {
                purchase_id: {
                    required: true,
                },
                "madin[]": {
                    required: true,
                },
                "dain[]": {
                    required: true,
                },
                "price_dain[]": {
                    required: true,
                    number: true
                },
                "price_madin[]": {
                    required: true,
                    number: true
                },
            },
            messages: {
                purchase_id: {
                    required: "يرجى إدخال فاتورة المشتريات.",
                },
                "madin[]": {
                    required: "يرجى إدخال الحساب المدين.",
                },
                "dain[]": {
                    required: "يرجى إدخال الحساب الدائن.",
                },
                "price_madin[]": {
                    required: "يرجى إدخال مبلغ الحساب المدين.",
                    number: "يجب أن يكون المبلغ عدداً."
                },
                "price_dain[]": {
                    required: "يرجى إدخال مبلغ الحساب الدائن.",
                    number: "يجب أن يكون المبلغ عدداً."
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

        document.getElementById('compoundForm').addEventListener('submit', function(e) {
            cleanNumbersBeforeSubmit();
        });

        attachCommaFormatter('.price_madin');
        attachCommaFormatter('.price_dain');

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
            let price_madin = document.querySelectorAll('.price_madin');
            let price_dain = document.querySelectorAll('.price_dain');
            let sum_madin = 0;
            let sum_dain = 0;
            price_madin.forEach(function(madin) {
                let price_madin_value = madin.value.replace(/,/g, '') || 0;
                sum_madin += parseFloat(price_madin_value);

            });

            price_dain.forEach(function(dain) {
                let price_dain_value = dain.value.replace(/,/g, '') || 0;
                sum_dain += parseFloat(price_dain_value);

            });

            document.getElementById('saveButton').disabled = (parseFloat(sum_madin) !== parseFloat(sum_dain));

        }

        function attachCommaFormatter(selector) {

            document.querySelectorAll(selector).forEach(input => {

                input.addEventListener('input', function() {

                    /* ـــ حالة المؤشر قبل التعديل ـــ */
                    const oldPos = this.selectionStart;
                    const oldVal = this.value;

                    /* 1) إزالة كل ما ليس رقمًا أو نقطة */
                    let cleaned = oldVal.replace(/[^0-9.]/g, '');

                    /* 2) الاحتفاظ بأول نقطة فقط ومسح البقية */
                    const firstDot = cleaned.indexOf('.');
                    if (firstDot !== -1) {
                        cleaned = cleaned.slice(0, firstDot + 1) +
                            cleaned.slice(firstDot + 1).replace(/\./g, '');
                    }

                    /* 3) تقسيم إلى جزء صحيح وعشرى */
                    const [rawInt = '', rawDec = ''] = cleaned.split('.');

                    /* 4) إضافة فواصل للجزء الصحيح */
                    const intPart = rawInt.replace(/^0+(?!$)/, ''); // أصفار زائدة
                    const intWithCommas = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                    /* 5) إعادة التجميع */
                    let formatted = intWithCommas;
                    if (firstDot !== -1) {
                        // لو المستخدم كتب نقطة ولم يكتب أرقام بعدها نحافظ على النقطة
                        formatted += '.' + rawDec;
                    }

                    this.value = formatted;

                    /* 6) إعادة المؤشر لموضعه الطبيعى */
                    const diff = formatted.length - oldVal.length;
                    this.setSelectionRange(oldPos + diff, oldPos + diff);
                });

            });
        }

        function cleanNumbersBeforeSubmit() {
            document.querySelectorAll('input[name^="price_madin"]').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });

            document.querySelectorAll('input[name^="price_dain"]').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
        }
    </script>
@endsection
