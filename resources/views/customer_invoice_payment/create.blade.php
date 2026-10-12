@extends('layouts.app')
@section('css')
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
    {{-- Select2 --}}
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <style>
        .text-danger {
            font-size: 13px;
            display: block;
            margin-top: 5px;
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> تحصيل فواتير العملاء</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    تحصيل فواتير</span>
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
                        <div class="alert alert-outline-danger" role="alert">
                            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                <span aria-hidden="true">&times;</span></button>
                            @foreach ($errors->all() as $error)
                                <strong>خطأ!</strong> {{ $error }}
                            @endforeach
                        </div>
                    @endif
                    <div class="col-sm-6 col-md-4 col-xl-12 mg-t-20">
                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('sale_invoice.index') }}" class="btn btn-primary">
                                <i class="fa fa-list"></i>&nbsp;&nbsp;قائمة فواتير المبيعات
                            </a>
                            <a href="javascript:history.back()" class="btn btn-warning">
                                <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer-invoice-payment.store') }}" method="post" id="paymentForm">
                        @csrf
                        <div class="form-group">
                            <span class="tx-danger">*</span><label for="customer_id">اسم العميل</label>
                            <select name="customer_id" id="customer_id" class="form-control" required style="width: 100%">
                                <option value="">اختر العميل</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->tree4_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group row align-items-center">
                            {{-- المبلغ نقدا --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="madin_name[0]" id="cash_amount"
                                        class="form-control madin_name" placeholder="المبلغ نقدا">
                                </div>
                            </div>

                            {{-- الخزنة --}}
                            <div class="col-md-8">
                                <div class="form-group">
                                    <select name="madin_id[0]" class="form-control" style="width:100%">
                                        <option value="">-- اختر الخزنة --</option>
                                        @foreach ($safes as $safe)
                                            <option value="{{ $safe->id }}">{{ $safe->tree4_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            {{-- المبلغ بالبنك --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="madin_name[1]" id="bank_amount"
                                        class="form-control madin_name" placeholder="المبلغ بالبنك">
                                </div>
                            </div>

                            {{-- رقم العملية --}}
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" name="transaction_no" id="transaction_no" class="form-control"
                                        placeholder="رقم العملية" style="display:none;">
                                </div>
                            </div>

                            {{-- اسم البنك --}}
                            <div class="col-md-8">
                                <div class="form-group">
                                    <select name="madin_id[1]" class="form-control" style="width: 100%;">
                                        <option value="">-- اختر البنك --</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->tree4_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="note">البيان</label>
                            <textarea name="note" id="note" class="form-control"></textarea>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">فواتير العميل غير المسددة</h6>
                            <button type="button" class="btn btn-secondary btn-sm" id="autoDistributeBtn">
                                <i class="fa fa-magic"></i>&nbsp;&nbsp;توزيع تلقائي
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-md-nowrap" id="invoicesTable" style="display:none;">
                                <thead>
                                    <tr>
                                        <th class="wd-5p border-bottom-0">#</th>
                                        <th class="wd-5p border-bottom-0"></th>
                                        <th class="wd-15p border-bottom-0">رقم الفاتورة</th>
                                        <th class="wd-15p border-bottom-0">التاريخ</th>
                                        <th class="wd-15p border-bottom-0">الاجمالي</th>
                                        <th class="wd-10p border-bottom-0">المسدد</th>
                                        <th class="wd-10p border-bottom-0">المتبقي</th>
                                        <th class="wd-15p border-bottom-0">المبلغ المسدد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>المبلغ المستلم</label>
                                <h4 id="received_total">0.00</h4>
                            </div>
                            <div class="col-md-4">
                                <label>المبلغ الموزع</label>
                                <h4 id="allocated_total">0.00</h4>
                            </div>
                            <div class="col-md-4">
                                <label>المتبقي (الباقي لدى الشركة)</label>
                                <h4 id="leftover_total">0.00</h4>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn ripple btn-primary" type="submit" id="submitBtn">حفظ التحصيل
                            </button>
                            <a href="javascript:history.back()" class="btn ripple btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- row closed -->
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <script>
        $('select').select2();

        const unformat = v => parseFloat(String(v).replace(/,/g, '')) || 0;
        const round2 = v => Math.round(v * 100) / 100;
        const fmt = v => round2(v).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        $(document).ready(function() {

            $('#paymentForm').validate({
                rules: {
                    customer_id: { required: true },
                    'madin_id[0]': {
                        required: {
                            depends: function() {
                                return unformat($('input[name="madin_name[0]"]').val()) > 0;
                            }
                        }
                    },
                    'madin_name[0]': {
                        required: {
                            depends: function() {
                                return $('select[name="madin_id[0]"]').val() !== '';
                            }
                        }
                    },
                    'madin_id[1]': {
                        required: {
                            depends: function() {
                                return unformat($('input[name="madin_name[1]"]').val()) > 0;
                            }
                        }
                    },
                    'madin_name[1]': {
                        required: {
                            depends: function() {
                                return $('select[name="madin_id[1]"]').val() !== '';
                            }
                        }
                    },
                    'transaction_no': {
                        required: {
                            depends: function() {
                                return unformat($('input[name="madin_name[1]"]').val()) > 0;
                            }
                        }
                    }
                },
                messages: {
                    customer_id: { required: "يرجى إدخال اسم العميل" },
                    'madin_id[0]': { required: 'يرجى اختيار الخزنة' },
                    'madin_name[0]': { required: 'يرجى إدخال المبلغ نقداً' },
                    'madin_id[1]': { required: 'يرجى اختيار البنك' },
                    'madin_name[1]': { required: 'يرجى إدخال المبلغ بالبنك' },
                    'transaction_no': { required: 'يرجى إدخال رقم عملية البنك' }
                },
                errorPlacement: function(error, element) {
                    if (element.closest('.input-group').length) {
                        error.insertAfter(element.closest('.input-group'));
                    } else if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                errorElement: "span",
                errorClass: "text-danger",
                highlight: function(element) { $(element).closest('.form-group').addClass('has-error'); },
                unhighlight: function(element) { $(element).closest('.form-group').removeClass('has-error'); },
                submitHandler: function(form) {
                    let allocated = 0;
                    $('#invoicesTable input[name^="allocations"]').each(function() {
                        const value = unformat(this.value);
                        if (value > 0) {
                            this.value = value.toFixed(2);
                            allocated += value;
                        } else {
                            this.removeAttribute('name');
                        }
                    });
                    if (allocated <= 0) {
                        notif({
                            msg: "يرجى تحديد مبلغ مسدد لأحد الفواتير",
                            type: "error"
                        });
                        return false;
                    }
                    cleanNumbersBeforeSubmit();
                    let $submitBtn = $(form).find('button[type="submit"]');
                    $submitBtn.prop('disabled', true);
                    $submitBtn.html(`<span class="spinner-border spinner-border-sm"></span> جاري حفظ البيانات...`);
                    form.submit();
                }
            });

            $('#bank_amount').on('input', function() {
                if (unformat($(this).val()) > 0) {
                    $('#transaction_no').show();
                } else {
                    $('#transaction_no').hide().val('');
                }
                recalcTotals();
            });

            $('#cash_amount').on('input', function() {
                recalcTotals();
            });

            // تحميل الفواتير غير المسددة عند تغيير العميل
            $('#customer_id').on('change', function() {
                const customerId = $(this).val();
                $('#invoicesTable tbody').empty();
                $('#invoicesTable').hide();
                recalcTotals();
                if (!customerId) {
                    return;
                }
                $.ajax({
                    url: '{{ route('customer-invoice-payment.unpaid') }}',
                    method: 'POST',
                    data: {
                        customer_id: customerId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(invoices) {
                        if (!invoices.length) {
                            return;
                        }
                        invoices.forEach(function(inv, index) {
                            const $tr = $('<tr>').attr('data-remaining', inv.remaining);

                            $('<td>').text(index + 1).appendTo($tr);

                            const $checkTd = $('<td>');
                            $('<input>', {
                                type: 'checkbox',
                                class: 'invoice-check'
                            }).appendTo($checkTd);
                            $checkTd.appendTo($tr);

                            const $numberTd = $('<td>').text(inv.invoice_number);
                            $numberTd.append(' ');
                            $('<span>', {
                                class: 'badge badge-warning partial-badge',
                                text: 'جزئي'
                            }).css('display', 'none').appendTo($numberTd);
                            $numberTd.appendTo($tr);

                            $('<td>').text(inv.date).appendTo($tr);
                            $('<td>').text(fmt(inv.total_amount)).appendTo($tr);
                            $('<td>').text(fmt(inv.paid)).appendTo($tr);
                            $('<td>').text(fmt(inv.remaining)).appendTo($tr);

                            const $amountTd = $('<td>');
                            $('<input>', {
                                type: 'text',
                                class: 'form-control alloc-input',
                                name: 'allocations[' + inv.id + ']',
                                value: '0.00'
                            }).appendTo($amountTd);
                            $amountTd.appendTo($tr);

                            $tr.appendTo('#invoicesTable tbody');
                        });
                        $('#invoicesTable').show();
                    }
                });
            });

            // عند التحديد: المبلغ = min(المتبقي, المتاح من المستلم لغير هذه الفاتورة)
            $(document).on('change', '.invoice-check', function() {
                const $row = $(this).closest('tr');
                const $input = $row.find('.alloc-input');
                const remaining = round2(parseFloat($row.attr('data-remaining')) || 0);
                if (this.checked) {
                    const pool = round2(receivedAmount() - otherRowsSum($input));
                    if (pool <= 0) {
                        this.checked = false;
                        return;
                    }
                    const amount = Math.min(remaining, pool);
                    $input.val(amount.toFixed(2));
                } else {
                    $input.val('0.00');
                }
                updateRowBadge($row);
                recalcTotals();
            });

            // تعديل المبلغ يدوياً: يقص بين 0 و min(المتبقي, المتاح + مبلغ الفاتورة الحالي)
            $(document).on('input', '.alloc-input', function() {
                const $row = $(this).closest('tr');
                const remaining = round2(parseFloat($row.attr('data-remaining')) || 0);
                const others = otherRowsSum($(this));
                const maxAllowed = Math.min(remaining, Math.max(0, round2(receivedAmount() - others)));
                let value = unformat(this.value);
                if (value > maxAllowed) {
                    value = maxAllowed;
                }
                if (value < 0) {
                    value = 0;
                }
                this.value = value.toFixed(2);
                $row.find('.invoice-check').prop('checked', value > 0);
                updateRowBadge($row);
                recalcTotals();
            });

            // توزيع تلقائي من الاقدم حتى ينفد المبلغ المستلم
            $('#autoDistributeBtn').on('click', function() {
                let pool = receivedAmount();
                $('#invoicesTable tbody tr').each(function() {
                    const $row = $(this);
                    const $check = $row.find('.invoice-check');
                    const $input = $row.find('.alloc-input');
                    const remaining = round2(parseFloat($row.attr('data-remaining')) || 0);
                    if (pool <= 0) {
                        $check.prop('checked', false);
                        $input.val('0.00');
                        updateRowBadge($row);
                        return;
                    }
                    const amount = Math.min(remaining, pool);
                    $check.prop('checked', true);
                    $input.val(amount.toFixed(2));
                    pool = round2(pool - amount);
                    updateRowBadge($row);
                });
                recalcTotals();
            });

            function receivedAmount() {
                return round2(unformat($('#cash_amount').val()) + unformat($('#bank_amount').val()));
            }

            function otherRowsSum($input) {
                let sum = 0;
                $('#invoicesTable input[name^="allocations"]').each(function() {
                    if (this !== $input[0]) {
                        sum += unformat(this.value);
                    }
                });
                return round2(sum);
            }

            function updateRowBadge($row) {
                const remaining = round2(parseFloat($row.attr('data-remaining')) || 0);
                const amount = unformat($row.find('.alloc-input').val());
                if (amount > 0 && amount < remaining) {
                    $row.find('.partial-badge').show();
                } else {
                    $row.find('.partial-badge').hide();
                }
            }

            function recalcTotals() {
                const received = receivedAmount();
                let allocated = 0;
                $('#invoicesTable input[name^="allocations"]').each(function() {
                    allocated += unformat(this.value);
                });
                allocated = round2(allocated);
                const leftover = round2(received - allocated);

                $('#received_total').text(fmt(received));
                $('#allocated_total').text(fmt(allocated));
                $('#leftover_total').text(fmt(leftover));

                if (leftover < 0) {
                    $('#leftover_total').addClass('text-danger');
                    $('#submitBtn').prop('disabled', true);
                } else {
                    $('#leftover_total').removeClass('text-danger');
                    $('#submitBtn').prop('disabled', false);
                }
            }

            attachCommaFormatter('.madin_name');
        });

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
            document.querySelectorAll('input[name^="madin_name"]').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
        }
    </script>
@endsection
