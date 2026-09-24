@extends('layouts.app')

@section('css')
    <!-- Internal Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .scrollable-card {
            max-height: 75vh;
            /* أو أي ارتفاع يناسبك */
            overflow-y: hidden;
            /* نخفي الاسكرول افتراضيًا */
            scrollbar-width: none;
            /* لإخفاء الأسهم في فايرفوكس */
        }

        /* إظهار الاسكرول عند الـ hover */
        .scrollable-card:hover {
            overflow-y: auto;
            /* نعيده عند تمرير الماوس */
            scrollbar-width: thin;
            /* فايرفوكس */
            scrollbar-color: #888 #f1f1f1;
            /* ألوان الاسكرول */
        }

        /* تنسيق الاسكرول في كروم وباقي المتصفحات */
        .scrollable-card::-webkit-scrollbar {
            width: 8px;
            display: none;
            /* نخفيه افتراضيًا */
        }

        .scrollable-card:hover::-webkit-scrollbar {
            display: block;
            /* نظهره عند hover */
        }

        .scrollable-card::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .scrollable-card::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .scrollable-card::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .invoice-card {
            height: 75vh;
            /* الكرت بطول الشاشة */
        }

        .invoice-body {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* فقط المنتجات تتحرك */
        .invoice-products {
            flex: 1;
            overflow-y: auto;
            /* اسكرول رأسي */
            overflow-x: auto;
            /* اسكرول أفقي */
            border: 1px solid #ddd;
            white-space: nowrap;
            /* يمنع التفاف الأعمدة */
        }

        .invoice-products table th,
        .invoice-products table td {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
            border-bottom: 2px solid #ccc;
            padding: 4px 6px !important;
            font-size: 13px;
            vertical-align: middle;
        }

        .invoice-products input {
            padding: 2px 6px;
            font-size: 13px;
        }


        /* الحسابات ثابتة */
        .invoice-summary {
            background: #f9f9f9;
            padding: 10px;
            border-top: 2px solid #ccc;
        }

        .form-row-inline {
            display: flex;
            align-items: center;
            /* gap: 1px; */
        }

        .form-row-inline label {
            /* min-width: 120px; */
            /* عرض ثابت لكل اللّيبلات */
            margin: 0;
            font-weight: 600;
            white-space: nowrap;
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto">إدارة فواتير المبيعات</h5>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ سداد فاتورة المبيعات</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')

    <!-- row opened -->

    <form id="payForm" action="{{ route('sale_invoice.pay.store', $sale_invoice->id) }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="card">
                    <div class="">
                        @if ($errors->any())
                            <div class="alert alert-outline-danger" role="alert">
                                <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                    <span aria-hidden="true">&times;</span></button>
                                @foreach ($errors->all() as $error)
                                    <strong>خطأ!</strong> {{ $error }} <br />
                                @endforeach
                            </div>
                        @endif
                        {{-- @if (session('error_created'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif --}}



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
                        {{-- <p class="tx-12 tx-gray-500 mb-2">Example of Valex Simple Table. <a href="">Learn more</a></p>  --}}
                    </div>
                    <div class="card-header">
                        <h3>فاتورة بالرقم {{ $sale_invoice->invoice_number }} للعميل
                            {{ $sale_invoice->customer->tree4_name }}</h3>
                    </div>
                    <div class="card-body">

                        <div class="modal-body">
                            <label for="">قيمة الفاتورة</label>
                            <input type="text" class="form-control"
                                value="{{ number_format($sale_invoice->deferend_amount) }}" disabled>

                            <div class="form-group">
                                <label>كاش</label>
                                <div class="row">
                                    <div class="col-md-7">
                                        <select id="cash_name" name="cash_name" class="form-control">
                                            @foreach ($safes as $safe)
                                                <option value="{{ $safe->id }}">{{ $safe->tree4_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="cash_amount_total" id="cash_amount_total"
                                            class="form-control" placeholder="المبلغ نقداً">
                                    </div>
                                </div>
                            </div>


                            <!-- Amount -->
                            <div class="form-group">
                                <label>بنك</label>
                                <div class="row">
                                    <div class="col-md-7">
                                        <select id="bank_name" name="bank_name" class="form-control">
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">
                                                    {{ $bank->tree4_name }} - {{ $bank->no_bank }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="bank_amount_total" id="bank_amount_total"
                                            class="form-control" placeholder="المبلغ بالبنك">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer justify-content-center">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fa fa-check"></i> تأكيد السداد
                            </button>
                            <a href="{{ route('sale_invoice.index') }}" class="btn btn-secondary">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
    </div>
    </div>

@endsection

@section('js')
    <!-- Internal Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#bank_name,#cash_name').select2({
                width: '100%'
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

            attachCommaFormatter('#cash_amount_total');
            attachCommaFormatter('#bank_amount_total');

            document.getElementById('payForm').addEventListener('submit', function(e) {
                cleanNumbersBeforeSubmit();
            });

            function cleanNumbersBeforeSubmit() {
                let singleFields = ['cash_amount_total', 'bank_amount_total', 'discount', 'sub_total', 'tax',
                    'total',
                    'deferend_amount'
                ];
                singleFields.forEach(id => {
                    let el = document.getElementById(id);
                    if (el && el.value) {
                        el.value = el.value.replace(/,/g, '');
                    }
                });
            }

        });
    </script>
@endsection
