@extends('layouts.app')
@section('css')
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--Internal  Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> إدارة النظام المحاسبي </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0"> / تحويل العملات</span>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إضافة تحويل</span>
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
                                    msg: " تم إضافة التحويل بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif
                    <div class="d-flex justify-content-between gap-2">
                        <h5 class="content-title mb-0 my-auto"> إضافة تحويل عملة جديد :</h5>

                        <a href="javascript:history.back()" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            <form id="currencyTransferForm" action="{{ route('currency-transfer.store') }}"
                                method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-4">
                                        <span class="tx-danger">*</span><label for="date" class="control-label"> التاريخ</label>
                                        <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>"
                                            class="form-control">
                                    </div>
                                    <div class="col-xl-4">
                                        <span class="tx-danger">*</span><label for="from_account_id"
                                            class="control-label"> من حساب</label>
                                        <select name="from_account_id" id="from_account_id"
                                            class="form-control select2-from">
                                            <option value="">-- من فضلك قم باختيار حساب --</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->tree4_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-4">
                                        <span class="tx-danger">*</span><label for="from_currency_id"
                                            class="control-label"> عملة التحويل</label>
                                        <select name="from_currency_id" id="from_currency_id" class="form-control">
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->id }}"
                                                    data-rate="{{ $currency->amount }}"
                                                    @selected($currency->is_base)>{{ $currency->name }}
                                                    ({{ $currency->symbol }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-4">
                                        <span class="tx-danger">*</span><label for="from_amount"
                                            class="control-label"> المبلغ</label>
                                        <input type="text" id="from_amount" name="from_amount" class="form-control"
                                            placeholder="يرجى ادخال المبلغ">
                                    </div>
                                    <div class="col-xl-4">
                                        <span class="tx-danger">*</span><label for="to_account_id"
                                            class="control-label"> إلى حساب</label>
                                        <select name="to_account_id" id="to_account_id" class="form-control select2-to">
                                            <option value="">-- من فضلك قم باختيار حساب --</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->tree4_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-4">
                                        <span class="tx-danger">*</span><label for="to_currency_id"
                                            class="control-label"> العملة المستلمة</label>
                                        <select name="to_currency_id" id="to_currency_id" class="form-control">
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->id }}"
                                                    data-rate="{{ $currency->amount }}"
                                                    @selected($currency->is_base)>{{ $currency->name }}
                                                    ({{ $currency->symbol }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-4">
                                        <label for="to_amount" class="control-label"> المبلغ المستلم</label>
                                        <input type="text" id="to_amount" name="to_amount" class="form-control"
                                            readonly>
                                        <small id="rate-line" class="text-muted"></small>
                                    </div>
                                    <div class="col-xl-8">
                                        <label for="note" class="control-label"> ملاحظة</label>
                                        <input type="text" id="note" name="note" class="form-control"
                                            placeholder="يرجى ادخال ملاحظة (اختياري)">
                                    </div>
                                </div>
                                <br>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary"> إضــافة التحويل</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>
    <script>
        $('.select2-from').select2();
        $('.select2-to').select2();

        function getRate(selector) {
            return parseFloat($(selector).find(':selected').data('rate')) || 0;
        }

        function formatWithCommas(value) {
            const parts = value.toFixed(2).split('.');
            return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '.' + parts[1];
        }

        function calcToAmount() {
            const fromRate = getRate('#from_currency_id');
            const toRate = getRate('#to_currency_id');
            const fromAmount = parseFloat($('#from_amount').val().replace(/,/g, '')) || 0;

            if (fromRate > 0 && toRate > 0) {
                $('#rate-line').text('سعر الصرف: 1 ' + $('#from_currency_id').find(':selected').text() + ' = ' +
                    formatWithCommas(fromRate / toRate) + ' ' + $('#to_currency_id').find(':selected').text());
            } else {
                $('#rate-line').text('');
            }

            $('#to_amount').val((fromRate > 0 && toRate > 0 && fromAmount > 0) ?
                formatWithCommas(fromAmount * fromRate / toRate) : '');
        }

        $('#from_currency_id, #to_currency_id').on('change', calcToAmount);
        $('#from_amount').on('input', calcToAmount);

        $('#currencyTransferForm').validate({
            rules: {
                date: {
                    required: true,
                },
                from_account_id: {
                    required: true,
                },
                to_account_id: {
                    required: true,
                    different: '#from_account_id',
                },
                from_currency_id: {
                    required: true,
                },
                to_currency_id: {
                    required: true,
                },
                from_amount: {
                    required: true,
                    normalizer: function(value) {
                        return value.replace(/,/g, '');
                    },
                    number: true,
                    min: 0.01,
                },
            },
            messages: {
                date: {
                    required: "يرجى إدخال التاريخ.",
                },
                from_account_id: {
                    required: "يرجى إدخال الحساب المصدر.",
                },
                to_account_id: {
                    required: "يرجى إدخال الحساب المستلم.",
                    different: "لا يمكن أن يكون الحساب المصدر والمستلم نفس الحساب.",
                },
                from_currency_id: {
                    required: "يرجى إدخال عملة التحويل.",
                },
                to_currency_id: {
                    required: "يرجى إدخال العملة المستلمة.",
                },
                from_amount: {
                    required: "يرجى إدخال المبلغ.",
                    number: "يجب أن يكون المبلغ عدداً.",
                    min: "يجب أن يكون المبلغ أكبر من صفر.",
                },
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
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });

        function attachCommaFormatter(selector) {

            document.querySelectorAll(selector).forEach(input => {

                input.addEventListener('input', function() {

                    const oldPos = this.selectionStart;
                    const oldVal = this.value;

                    let cleaned = oldVal.replace(/[^0-9.]/g, '');

                    const firstDot = cleaned.indexOf('.');
                    if (firstDot !== -1) {
                        cleaned = cleaned.slice(0, firstDot + 1) +
                            cleaned.slice(firstDot + 1).replace(/\./g, '');
                    }

                    const [rawInt = '', rawDec = ''] = cleaned.split('.');

                    const intPart = rawInt.replace(/^0+(?!$)/, '');
                    const intWithCommas = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                    let formatted = intWithCommas;
                    if (firstDot !== -1) {
                        formatted += '.' + rawDec;
                    }

                    this.value = formatted;

                    const diff = formatted.length - oldVal.length;
                    this.setSelectionRange(oldPos + diff, oldPos + diff);
                });

            });
        }

        attachCommaFormatter('#from_amount');
    </script>
@endsection
