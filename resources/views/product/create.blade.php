@extends('layouts.app')
@section('css')
    <!-- Internal Data table css -->
@endsection
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto">إدارة المنتجات</h5>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إضافة منتج</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    @if ($errors->any())
                        <div class="alert alert-outline-danger" role="alert">
                            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            @foreach ($errors->all() as $error)
                                <strong>خطأ!</strong> {{ $error }}
                            @endforeach
                        </div>
                    @endif
                    @if (session()->has('success_created'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: "تم حفظ البيانات بنجاح",
                                    type: "success"
                                });
                            };
                        </script>
                    @endif
                </div>
                <div class="card-body">
                    <form id="productForm" action="{{ route('product.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <!-- Barcode -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="barcode">باركود المنتج</label>
                                    <input type="text" name="barcode" id="barcode" class="form-control"
                                        placeholder="ادخل باركود المنتج" autocomplete="off">
                                </div>
                            </div>
                            <!-- Name -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <span class="tx-danger">*</span><label for="name">اسم المنتج </label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="ادخل اسم المنتج" autocomplete="off">
                                </div>
                            </div>
                            <!-- Name EN -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name_en">الاسم العلمي للمنتج </label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        placeholder="ادخل الاسم العلمي للمنتج" autocomplete="off">
                                </div>
                            </div>
                            <!-- Category -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <span class="tx-danger">*</span><label for="category_id">شركة المنتج </label>
                                    <select class="form-control" name="category_id" id="category_id">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- Reorder Level -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reorder_level">إعادة الطلب </label>
                                    <div class="input-group">
                                        <input type="number" name="reorder_level" id="reorder_level" class="form-control"
                                            style="flex: 2" placeholder="ادخل اعادة حد الطلب">
                                        <select name="unit_reorder_level" id="unit_reorder_level" class="form-control"
                                            style="flex: 1">
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Expire Date -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="expire_date">تاريخ انتهاء الصلاحية </label>
                                    <input type="date" name="expire_date" id="expire_date" class="form-control">
                                </div>
                            </div>
                            <!-- Units Section -->
                            <div id="unit-rows" class="col-12">
                                <br>
                                <br>
                                <br>
                                <div class="unit-row mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="unit_id" class="form-label">الوحدة</label>
                                            <select name="units[0][unit_id]" id="unit_id" class="form-control">
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="price" class="form-label">سعر البيع</label>
                                            <input type="text" name="units[0][price]" id="price"
                                                class="form-control product_price" placeholder="ادخل سعر بيع الوحدة"
                                                value="{{ old('units.0.price', 0) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="price_purchase" class="form-label">سعر الشراء</label>
                                            <input type="text" name="units[0][price_purchase]" id="price_purchase"
                                                class="form-control product_price_purchase"
                                                placeholder="ادخل سعر الشراء الوحدة"
                                                value="{{ old('units.0.price_purchase', 0) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" name="units[0][conversion_factor]"
                                                id="conversion_factor" class="form-control product_conversion_factor"
                                                placeholder="ادخل كمية الوحدة" value="1" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="button" id="add-unit-row" class="btn btn-secondary mb-3">إضافة
                                    وحدة</button>

                                {{-- <button type="button" id="add-stock-row" class="btn btn-info mb-3 mr-3">إضافة
                                    مخزن</button> --}}
                            </div>
                            <!-- Submit Button -->
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary">حفظ</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('select').select2();
            let unitIndex = 1;
            let stockIndex = 1;

            // إضافة صف جديد للوحدات
            $('#add-unit-row').click(function() {
                let unitRow = `<div class="unit-row mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="unit_id_${unitIndex}" class="form-label">الوحدة</label>
                                            <select name="units[${unitIndex}][unit_id]" id="unit_id_${unitIndex}" class="form-control product_unit" required>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="price_${unitIndex}" class="form-label">سعر البيع</label>
                                            <input type="text" name="units[${unitIndex}][price]" id="price_${unitIndex}" class="form-control product_price" placeholder="ادخل سعر بيع الوحدة" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="price_purchase_${unitIndex}" class="form-label">سعر الشراء</label>
                                            <input type="text" name="units[${unitIndex}][price_purchase]" id="price_purchase_${unitIndex}" class="form-control product_price_purchase" placeholder="ادخل سعر شراء الوحدة" value="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="conversion_factor_${unitIndex}" class="form-label">معامل الوحدة</label>
                                            <input type="text" name="units[${unitIndex}][conversion_factor]" id="conversion_factor_${unitIndex}" class="form-control product_conversion_factor" required placeholder="كم تساوي من اصغر وحدة">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger remove-unit-row">حذف</button>
                                        </div>
                                    </div>
                                </div>`;

                $('#unit-rows').append(unitRow);

                // تفعيل select2 على select الجديد
                $(`#unit_id_${unitIndex}`).select2();

                // إضافة قواعد التحقق للحقول الجديدة
                $(`#unit_id_${unitIndex}`).rules('add', {
                    required: true,
                    messages: {
                        required: "يرجى اختيار الوحدة"
                    }
                });

                $(`#price_${unitIndex}`).rules('add', {
                    number: true,
                    messages: {
                        number: "يجب أن يكون سعر البيع عددًا"
                    }
                });

                $(`#price_purchase_${unitIndex}`).rules('add', {
                    number: true,
                    messages: {
                        number: "يجب أن يكون سعر الشراء عددًا"
                    }
                });

                $(`#conversion_factor_${unitIndex}`).rules('add', {
                    required: true,
                    number: true,
                    messages: {
                        required: "يرجى إدخال عامل التحويل",
                        number: "يجب أن يكون عامل التحويل عددًا"
                    }
                });

                // **هنا: استدعاء دالة attachCommaFormatter على الحقول الجديدة فقط**
                attachCommaFormatter(`#price_${unitIndex}`);
                attachCommaFormatter(`#price_purchase_${unitIndex}`);
                attachCommaFormatter(`#conversion_factor_${unitIndex}`);

                unitIndex++;
            });

            // إزالة سف الوحدات عند الضغط على زر "حذف"
            $(document).on('click', '.remove-unit-row', function() {
                $(this).closest('.unit-row').remove();
            });

            // تفعيل التحقق من صحة النموذج
            $('#productForm').validate({
                rules: {
                    barcode: {
                        uniqueBarcode: true // قاعدة التحقق المخصصة الباركود
                    },
                    name: {
                        required: true,
                        uniqueName: true // قاعدة التحقق المخصصة للاسم
                    },
                    category_id: {
                        required: true
                    },
                    reorder_level: {
                        number: true
                    },
                    'units[0][unit_id]': {
                        required: true,
                    },
                    'units[0][price]': {
                        number: true,
                    },
                    'units[0][price_purchase]': {
                        number: true,
                    },
                    'units[0][conversion_factor]': {
                        required: true,
                    },
                    'stocks[0][quantity]': {
                        required: true,
                    },
                },
                messages: {
                    barcode: {
                        uniqueBarcode: "هذا الباركود موجود بالفعل في النظام"
                    },
                    name: {
                        required: "يرجى إدخال اسم المنتج",
                        uniqueName: "اسم المنتج موجود بالفعل في النظام"
                    },
                    category_id: {
                        required: "يرجى اختيار شركة المنتج"
                    },
                    'stock_id[]': {
                        required: "يرجى اختيار مخزن المنتج"
                    },
                    purchase_price: {
                        number: "يجب أن يكون سعر الشراء رقمًا"
                    },
                    reorder_level: {
                        number: "يجب أن يكون مستوى إعادة الطلب رقمًا"
                    },
                    'units[0][unit_id]': {
                        required: 'يرجى اختيار الوحدة'
                    },
                    'units[0][price]': {
                        number: 'يجب أن يكون سعر البيع رقمًا'
                    },
                    'units[0][price_purchase]': {
                        number: 'يجب أن يكون سعر الشراء رقمًا'
                    },
                    'stocks[0][quantity]': {
                        required: 'يرجى ادخال الكمية للمخزن'
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

            $.validator.addMethod("uniqueBarcode", function(value, element) {
                let isValid = false;
                $.ajax({
                    url: '{{ route('product.checkBarcode') }}', // مسار التحقق من الباركود
                    data: {
                        barcode: value,
                    },
                    type: 'GET',
                    async: false, // تأكد من أن التحقق يتم بشكل متزامن
                    success: function(response) {
                        isValid = response.isUnique; // إذا كانت النتيجة صحيحة (فريد)
                    }
                });
                return isValid;
            }, "هذا الباركود موجود بالفعل في النظام.");

            // إضافة قاعدة التحقق الفريدة للاسم باستخدام AJAX
            $.validator.addMethod("uniqueName", function(value, element) {
                var isValid = false;
                $.ajax({
                    url: '{{ route('product.checkName') }}', // مسار التحقق من الاسم
                    data: {
                        name: value
                    },
                    type: 'GET',
                    async: false, // تأكد من أن التحقق يتم بشكل متزامن
                    success: function(response) {
                        isValid = response.isUnique; // إذا كانت النتيجة صحيحة (فريد)
                    }
                });
                return isValid;
            }, "اسم المنتج موجود بالفعل في النظام.");

            // إضافة قاعدة التحقق المخصصة لتاريخ اليوم أو بعده
            $.validator.addMethod("after_or_equal_today", function(value, element) {
                var today = new Date();
                today.setHours(0, 0, 0, 0); // إزالة الوقت لضمان مقارنة صحيحة

                var date = new Date(value);
                date.setHours(0, 0, 0, 0); // إزالة الوقت من التاريخ المدخل

                return date >= today; // السماح بتاريخ اليوم أو الأيام القادمة فقط
            }, "يجب أن يكون تاريخ انتهاء الصلاحية اليوم أو بعده.");

            document.getElementById('productForm').addEventListener('submit', function(e) {
                cleanNumbersBeforeSubmit();
            });

            attachCommaFormatter('#price');
            attachCommaFormatter('#price_purchase');
            attachCommaFormatter('#conversion_factor');


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
            // الحقول ذات الأسماء المتكررة كمصفوفات (price[], row_sub_total[], qty[] ... الخ)
            // نفترض ان الفورم يحتوي على هذه الحقول، نمر عليها كلها
            document.querySelectorAll('.product_price').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
            document.querySelectorAll('.product_price_purchase').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
            document.querySelectorAll('.product_conversion_factor').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
        }
    </script>
@endsection
