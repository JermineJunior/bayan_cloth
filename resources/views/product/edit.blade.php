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
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تعديل منتج</span>
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
                    <form id="productForm" action="{{ route('product.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Barcode -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="barcode">باركود المنتج</label>
                                    <input type="text" name="barcode" id="barcode" class="form-control"
                                        value="{{ $product->barcode }}" placeholder="ادخل باركود المنتج">
                                </div>
                            </div>
                            <!-- Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">اسم المنتج <span class="tx-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ $product->name }}" placeholder="ادخل اسم المنتج">
                                </div>
                            </div>
                            <!-- Category -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">قسم المنتج <span class="tx-danger">*</span></label>
                                    <select class="form-control" name="category_id" id="category_id">
                                        @foreach ($categories as $category)
                                            <option {{ $product->category_id == $category->id ? 'selected' : '' }}
                                                value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- Purchase Price -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_price">سعر الشراء <span class="tx-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="purchase_price" id="purchase_price" class="form-control"
                                            value="{{ $product->purchase_price }}" step="0.01" placeholder="أدخل السعر"
                                            style="flex: 2">
                                        <select name="unit_purchase_price" id="unit_purchase_price" class="form-control"
                                            style="flex: 1">
                                            @foreach ($units as $unit)
                                                <option {{ $unit->id == $product->unit_purchase_price ? 'selected' : '' }}
                                                    value="{{ $unit->id }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Reorder Level -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reorder_level">إعادة الطلب <span class="tx-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="reorder_level" id="reorder_level" class="form-control"
                                            value="{{ $reorder_level }}" style="flex: 2" placeholder="ادخل اعادة حد الطلب">
                                        <select name="unit_reorder_level" id="unit_reorder_level" class="form-control"
                                            style="flex: 1">
                                            @foreach ($units as $unit)
                                                <option {{ $product->unit_reorder_level == $unit->id ? 'selected' : '' }}
                                                    value="{{ $unit->id }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Expire Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expire_date">تاريخ انتهاء الصلاحية <span class="tx-danger">*</span></label>
                                    <input type="date" name="expire_date" id="expire_date" class="form-control"
                                        value="{{ $product->expire_date }}">
                                </div>
                            </div>
                            <!-- Units Section -->
                            <div id="unit-rows" class="col-12">
                                <br>
                                <br>
                                <br>
                                <div class="unit-row mb-3">
                                    @foreach ($product->units as $item)
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="unit_id" class="form-label">الوحدة</label>
                                                <select name="units[{{ $loop->index }}][unit_id]"
                                                    id="unit_id_{{ $loop->index }}" class="form-control product_unit">
                                                    @foreach ($units as $unit)
                                                        <option {{ $unit->id == $item->pivot->unit_id ? 'selected' : '' }}
                                                            value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="price" class="form-label">السعر</label>
                                                <input type="number" name="units[{{ $loop->index }}][price]"
                                                    id="price_{{ $loop->index }}" class="form-control product_price"
                                                    step="0.01" placeholder="ادخل سعر الوحدة"
                                                    value="{{ $item->pivot->price }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="conversion_factor" class="form-label">الكمية</label>
                                                <input type="number"
                                                    name="units[{{ $loop->index }}][conversion_factor]"
                                                    id="conversion_factor_{{ $loop->index }}"
                                                    class="form-control product_conversion_factor"
                                                    placeholder="ادخل كمية الوحدة"
                                                    value="{{ $item->pivot->conversion_factor }}">
                                            </div>
                                            @if ($loop->index != 0)
                                                <div class="col-md-1 d-flex align-items-end mb-3">
                                                    <button type="button"
                                                        class="btn btn-danger remove-unit-row">حذف</button>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="button" id="add-unit-row" class="btn btn-secondary mb-3">إضافة
                                    وحدة</button>
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
            let unitIndex = $('.unit-row').children().length;

            // إضافة صف جديد للوحدات
            $('#add-unit-row').click(function() {
                let unitRow = `<div class="unit-row mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="unit_id_${unitIndex}" class="form-label">الوحدة</label>
                            <select name="units[${unitIndex}][unit_id]" id="unit_id_${unitIndex}" class="form-control product_unit" required>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="price_${unitIndex}" class="form-label">السعر</label>
                            <input type="number" name="units[${unitIndex}][price]" id="price_${unitIndex}" class="form-control product_price" step="0.01" required placeholder="ادخل سعر الوحدة">
                        </div>
                        <div class="col-md-3">
                            <label for="conversion_factor_${unitIndex}" class="form-label">الكمية</label>
                            <input type="number" name="units[${unitIndex}][conversion_factor]" id="conversion_factor_${unitIndex}" class="form-control product_conversion_factor" required placeholder="ادخل كمية عامل التحويل">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-unit-row">حذف</button>
                        </div>
                    </div>
                </div>`;

                $('#unit-rows').append(unitRow);

                $(`#unit_id_${unitIndex}`).select2();

                // إضافة قواعد التحقق للحقول الجديدة
                $(`#unit_id_${unitIndex}`).rules('add', {
                    required: true,
                    messages: {
                        required: "يرجى اختيار الوحدة"
                    }
                });

                $(`#price_${unitIndex}`).rules('add', {
                    required: true,
                    number: true,
                    messages: {
                        required: "يرجى إدخال سعر الوحدة",
                        number: "يجب أن يكون السعر عددًا"
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

                unitIndex++;
            });

            // إزالة سف الوحدات عند الضغط على زر "حذف"
            $(document).on('click', '.remove-unit-row', function() {
                $(this).closest('.unit-row').remove();
            });

            // تفعيل التحقق من صحة النموذج
            $('#productForm').validate({
                rules: {
                    name: {
                        required: true,
                    },
                    category_id: {
                        required: true
                    },
                    purchase_price: {
                        required: true,
                        number: true
                    },
                    reorder_level: {
                        required: true,
                        number: true
                    },
                    expire_date: {
                        required: true,
                        date: true,
                        after_or_equal_today: true // قاعدة التحقق المخصصة لتاريخ اليوم أو بعده
                    }
                },
                messages: {
                    name: {
                        required: "يرجى إدخال اسم المنتج",
                    },
                    category_id: {
                        required: "يرجى اختيار قسم المنتج"
                    },
                    'stock_id[]': {
                        required: "يرجى اختيار مخزون المنتج"
                    },
                    purchase_price: {
                        required: "يرجى إدخال سعر الشراء",
                        number: "يجب أن يكون سعر الشراء رقمًا"
                    },
                    reorder_level: {
                        required: "يرجى إدخال مستوى إعادة الطلب",
                        number: "يجب أن يكون مستوى إعادة الطلب رقمًا"
                    },
                    expire_date: {
                        required: "يرجى إدخال تاريخ انتهاء الصلاحية"
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

            $("#productForm").validate();

            function addValidationRules() {
                $('select.product_unit').each(function() {
                    $(this).rules("add", {
                        required: true,
                        messages: {
                            required: "يرجى اختيار وحدة المنتج."
                        }
                    });
                });

                $('input.product_price').each(function() {
                    $(this).rules("add", {
                        required: true,
                        number: true,
                        messages: {
                            required: "يرجى إدخال سعر المنتج.",
                            number: "يجب أن يكون السعر رقمًا."
                        }
                    });
                });

                $('input.product_conversion_factor').each(function() {
                    $(this).rules("add", {
                        required: true,
                        number: true,
                        messages: {
                            required: "يرجى إدخال الكمية.",
                            number: "يجب أن تكون الكمية رقمًا."
                        }
                    });
                });
            }

            // تطبيق القواعد عند تحميل الصفحة
            addValidationRules();


            // التحقق عند الإرسال
            $("#productForm").on("submit", function(e) {
                if (!$("#productForm").valid()) {
                    e.preventDefault();
                }
            });

            // إضافة قاعدة التحقق الفريدة للباركود باستخدام AJAX
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

        });
    </script>
@endsection
