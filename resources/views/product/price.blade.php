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
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ الارصدة الافتتاحية للمنتج</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <!-- بيانات المنتج -->
            <div class="col-md-12">
                <div class="row">
                    <!-- باركود المنتج -->
                    <div class="col-md-3">
                        <div class="card text-center shadow-sm border-primary">
                            <div class="card-body">
                                <h6 class="text-muted"><i class="fas fa-barcode"></i> باركود المنتج</h6>
                                <h4 class="font-weight-bold text-primary">{{ $product->barcode }}</h4>
                            </div>
                        </div>
                    </div>
                    <!-- اسم المنتج -->
                    <div class="col-md-3">
                        <div class="card text-center shadow-sm border-success">
                            <div class="card-body">
                                <h6 class="text-muted"><i class="fas fa-box"></i> اسم المنتج</h6>
                                <h4 class="font-weight-bold text-success">{{ $product->name }}</h4>
                            </div>
                        </div>
                    </div>
                    <!-- قسم المنتج -->
                    <div class="col-md-3">
                        <div class="card text-center shadow-sm border-warning">
                            <div class="card-body">
                                <h6 class="text-muted"><i class="fas fa-layer-group"></i> قسم المنتج</h6>
                                <h4 class="font-weight-bold text-warning">{{ $product->category->name }}</h4>
                            </div>
                        </div>
                    </div>
                    <!-- تاريخ انتهاء الصلاحية -->
                    <div class="col-md-3">
                        <div class="card text-center shadow-sm border-danger">
                            <div class="card-body">
                                <h6 class="text-muted"><i class="fas fa-calendar-alt"></i> تاريخ انتهاء الصلاحية</h6>
                                <h4 class="font-weight-bold text-danger">{{ $product->expire_date }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- فورم إضافة المخزون -->
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <form id="productForm" action="{{ route('product.opening.store', $product->id) }}" method="POST">
                            @csrf
                            <div id="stock-rows">
                                @if ($product->stocks()->where('quantity', '!=', 0)->count() == 0)
                                    <div class="stock-row mb-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="stock_id" class="form-label">المخزن</label>
                                                <select name="stocks[0][stock_id]" class="form-control product_stock">
                                                    @foreach ($stocks as $stock)
                                                        <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="quantity" class="form-label">الكمية</label>
                                                <input type="number" name="stocks[0][quantity]"
                                                    class="form-control product_quantity" step="0.01" min="1"
                                                    placeholder="ادخل كمية المخزون">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="unit_stock" class="form-label">الوحدة</label>
                                                <select name="stocks[0][unit_stock]" class="form-control product_unit">
                                                    @foreach ($product->units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @foreach ($product->stocks()->where('quantity' , '!=', 0)->get() as $item)
                                        <div class="stock-row mb-3">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="stock_id" class="form-label">المخزن</label>
                                                    <select name="stocks[{{ $loop->index }}][stock_id]"
                                                        class="form-control product_stock">
                                                        @foreach ($stocks as $stock)
                                                            <option
                                                                {{ $stock->id == $item->pivot->stock_id ? 'selected' : '' }}
                                                                value="{{ $stock->id }}">{{ $stock->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="quantity" class="form-label">الكمية</label>
                                                    <input type="number" name="stocks[{{ $loop->index }}][quantity]"
                                                        class="form-control product_quantity" step="0.01" min="1"
                                                        placeholder="ادخل كمية المخزون"
                                                        value="{{ $item->pivot->quantity / $product->units()->firstWhere('unit_id',$item->pivot->unit_stock)->pivot->conversion_factor }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="unit_stock" class="form-label">الوحدة</label>
                                                    <select name="stocks[{{ $loop->index }}][unit_stock]"
                                                        class="form-control product_unit">
                                                        @foreach ($product->units as $unit)
                                                            <option
                                                                {{ $unit->id == $item->pivot->unit_stock ? 'selected' : '' }}
                                                                value="{{ $unit->id }}">{{ $unit->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <!-- زر إضافة مخزون -->
                            <div class="col-12">
                                <button type="button" id="add-stock-row" class="btn btn-secondary mb-3">
                                    <i class="fas fa-plus-circle"></i> إضافة مخزون
                                </button>
                            </div>

                            <!-- زر الحفظ -->
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
                            </div>
                        </form>
                    </div>
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
            let stockIndex = $('.stock-row').children().length;


            // اضافة صف جديد للمخزون
            $('#add-stock-row').click(function() {
                let stockRow = `<div class="stock-row mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label for="stock_id_${stockIndex}" class="form-label">المخزن</label>
                        <select name="stocks[${stockIndex}][stock_id]" id="stock_id_${stockIndex}" class="form-control product_stock" required>
                            @foreach ($stocks as $stock)
                                <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="quantity_${stockIndex}" class="form-label">الكمية</label>
                        <input type="number" name="stocks[${stockIndex}][quantity]" id="quantity_${stockIndex}" class="form-control product_quantity" step="0.01" required placeholder="ادخل كمية المخزن">
                    </div>
                    <div class="col-md-4">
                        <label for="unit_stock_${stockIndex}" class="form-label">الوحدة</label>
                        <select name="stocks[${stockIndex}][unit_stock]" id="unit_stock_${stockIndex}" class="form-control product_unit" required>
                            @foreach ($product->units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-stock-row">حذف</button>
                    </div>
                </div>
            </div>`;

                $('#stock-rows').append(stockRow);

                $(`#stock_id_${stockIndex}`).select2();
                $(`#unit_stock_${stockIndex}`).select2();

                // إضافة قواعد التحقق للحقول الجديدة
                $(`#stock_id_${stockIndex}`).rules('add', {
                    required: true,
                    messages: {
                        required: "يرجى اختيار المخزون"
                    }
                });

                $(`#unit_id${stockIndex}`).rules('add', {
                    required: true,
                    messages: {
                        required: "يرجى اختيار الوحدة"
                    }
                });

                $(`#quantity_${stockIndex}`).rules('add', {
                    required: true,
                    number: true,
                    messages: {
                        required: "يرجى إدخال الكمية للمخزون",
                        number: "يجب أن يكون الكمية عددًا"
                    }
                });

                stockIndex++;
            });

            // إزالة صف المخزون عند الضغط على زر "حذف"
            $(document).on('click', '.remove-stock-row', function() {
                $(this).closest('.stock-row').remove();
            });


            // تفعيل التحقق من صحة النموذج
            $('#productForm').validate({
                rules: {
                    'stocks[0][stock_id]': {
                        required: true
                    },
                    'stocks[0][quantity]': {
                        required: true,
                        number: true
                    },
                    'stocks[0][unit_id]': {
                        required: true
                    },
                },
                messages: {
                    'stocks[0][stock_id]': {
                        required: "يرجى اختيار المخزون"
                    },
                    'stocks[0][quantity]': {
                        required: "يرجى ادخال الكمية للمخزون",
                        number: "يجب أن يكون الكمية عدداً"
                    },
                    'stocks[0][unit_id]': {
                        required: "يرجى اختيار الوحدة"
                    },
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

            $('form').on('submit', function(e) {
                $('select.stock_id').each(function() {
                    $(this).rules("add", {
                        required: true,
                        messages: {
                            required: "يرجى إدخال المخزون."
                        }
                    });
                });

                $('input.product_quantity').each(function() {
                    $(this).rules("add", {
                        required: true,
                        number: true,
                        messages: {
                            required: "يرجى إدخال الكمية للمخزون",
                            number: "يجب أن يكون الكمية عددًا"
                        }
                    });
                });

                $('select.unit_stock').each(function() {
                    $(this).rules("add", {
                        required: true,
                        messages: {
                            required: "يرجى إدخال الوحدة."
                        }
                    });
                });
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
