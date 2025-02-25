@extends('layouts.app')

@section('css')
    <!-- Internal Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto">إدارة فواتير مردودات المشتريات</h5>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ فاتورة مردودات المشتريات جديدة</span>
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
                                <strong>خطأ!</strong> {{ $error }} <br />
                            @endforeach
                        </div>
                    @endif
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
                <div class="card-body">
                    <form id="invoiceForm" action="{{ route('purchase_invoice_return.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="invoice_date">تاريخ الفاتورة</label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                        value="{{ old('invoice_date', $purchase_invoice->invoice_date) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="invoice_number">رقم فاتورة </label>
                                    <input type="text" name="invoice_number" id="invoice_number" class="form-control"
                                        value="{{ old('invoice_number', $invoice_number) }}" readonly>
                                </div>
                            </div>
                            @if ($purchase_invoice)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="invoice_number">رقم فاتورة المشتريات</label>
                                        <input type="text" name="purchase_invoice_number" id="purchase_invoice_number"
                                            class="form-control"
                                            value="{{ old('purchase_invoice_number', $purchase_invoice->invoice_number) }}"
                                            readonly>
                                        <input type="hidden" name="purchase_invoice_id" value="{{ $purchase_invoice->id }}">
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="supplier_id">المورد</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control">
                                        <option value="">عميل كاش</option>
                                        @foreach ($suppliers as $supplier)
                                            <option {{ $purchase_invoice->supplier_id == $supplier->id ? 'selected' : '' }}
                                                value="{{ $supplier->id }}">{{ $supplier->tree4_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_status">الحالة</label>
                                    <select name="payment_status" id="payment_status" class="form-control">
                                        <option {{ $purchase_invoice->payment_status == 0 ? 'selected' : '' }} value="0">
                                            مدفوع</option>
                                        <option {{ $purchase_invoice->payment_status == 1 ? 'selected' : '' }} value="1">
                                            آجل</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_status">المخزن</label>
                                    <select name="stock_id" id="stock_id" class="form-control"
                                        data-stock_id="{{ $purchase_invoice->stock_id }}">
                                        @foreach ($stocks as $stock)
                                            <option {{ $purchase_invoice->stock_id == $stock->id ? 'selected' : '' }}
                                                value="{{ $stock->id }}">{{ $stock->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>الوحدة</th>
                                        <th>الكمية</th>
                                        <th>السعر</th>
                                        <th>الإجمالي</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="product-rows">
                                    @if ($purchase_invoice->details()->count() > 0)
                                        @foreach ($purchase_invoice->details as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <input type="text" id="product_name_{{ $loop->iteration }}"
                                                        name="product_name[{{ $loop->iteration }}]"
                                                        class="form-control product_name"
                                                        value="{{ $item->product->name }}" readonly>
                                                    <input type="hidden" id="product_id_{{ $loop->iteration }}"
                                                        name="product_id[{{ $loop->iteration }}]" class="product_id"
                                                        value="{{ $item->product_id }}">
                                                </td>
                                                <td>
                                                    <select id="unit_id_{{ $loop->iteration }}"
                                                        class="form-control unit_id" name="unit_id[{{ $loop->iteration }}]"
                                                        data-product-id="{{ $item->product_id }}"
                                                        data-unit_id="{{ $item->unit_id }}">
                                                        @foreach ($item->product->units as $unit)
                                                            <option
                                                                {{ $item->unit_id == $unit->pivot->unit_id ? 'selected' : '' }}
                                                                value="{{ $unit->pivot->unit_id }}">
                                                                {{ $unit->name }}</option>
                                                            @if ($item->unit_id == $unit->pivot->unit_id)
                                                                @break
                                                            @endif
                                                        @endforeach

                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" id="qty_{{ $loop->iteration }}"
                                                        class="form-control qty" name="qty[{{ $loop->iteration }}]"
                                                        data-qty="{{ $item->qty }}" step="1" min="1"
                                                        value="{{ $item->qty }}">
                                                </td>
                                                <td>
                                                    <input type="number" id="price_{{ $loop->iteration }}"
                                                        class="form-control price" name="price[{{ $loop->iteration }}]"
                                                        step="0.01" min="0" value="{{ $item->price }}">
                                                </td>
                                                <td>
                                                    <input type="number" id="row_sub_total_{{ $loop->iteration }}"
                                                        class="form-control row_sub_total"
                                                        name="row_sub_total[{{ $loop->iteration }}]" step="0.01"
                                                        value="{{ $item->row_sub_total }}" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger remove-product"><i
                                                            class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"></td>
                                        <th>الاجمالي قبل الضريبة</th>
                                        <td colspan="2"><input type="number" class="form-control sub_total"
                                                id="sub_total" name="sub_total"
                                                value="{{ $purchase_invoice->sub_total_amount }}" min="0" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <th>قيمة الضريبة(17%)</th>
                                        <td colspan="2"><input type="number" class="form-control tax" id="tax"
                                                name="tax" value="{{ $purchase_invoice->tax_amount }}" min="0"
                                                readonly></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <th>الخصم</th>
                                        <td colspan="2"><input type="number" class="form-control discount"
                                                id="discount" name="discount" step="0.01"
                                                value="{{ $purchase_invoice->discount_amount ?? 0.0 }}" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <th> الاجمالي النهائي</th>
                                        <td colspan="2"><input type="number" class="form-control total"
                                                id="total" name="total" value="{{ $purchase_invoice->total_amount }}"
                                                min="0" readonly>
                                        </td>
                                    </tr>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-primary" id="save-invoice">حفظ الفاتورة</button>
                            </div>
                        </div>
                        <!-- زر الحفظ -->

                        <!-- Modal -->
                        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog"
                            aria-labelledby="paymentModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="paymentModalLabel">وسيلة الدفع</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {{-- <form id="payment-form"> --}}
                                        <div class="form-group">
                                            <label for="payment_method">اختر وسيلة الدفع</label>
                                            <select name="payment_method" id="payment_method" class="form-control"
                                                style="width: 100%" required>
                                                <option value="">-- اختر وسيلة الدفع --</option>
                                                <option value="1">نقداً</option>
                                                <option value="2">بنكي</option>
                                                <option value="3">كاش وبنكك</option>
                                            </select>
                                        </div>

                                        <div class="form-group d-none" id="cash-list">
                                            <label for="cash_name">اختر الخزينة</label>
                                            <select name="cash_id" id="cash_name" class="form-control"
                                                style="width: 100%"></select>
                                        </div>

                                        <div class="form-group d-none" id="bank-list">
                                            <label for="bank_name">اختر البنك</label>
                                            <select name="bank_id" id="bank_name" class="form-control"
                                                style="width: 100%"></select>
                                        </div>

                                        <div class="form-group d-none" id="cash-input">
                                            <label for="cash_amount">المبلغ نقداً</label>
                                            <input type="number" name="cash_amount" id="cash_amount"
                                                class="form-control" min="0" placeholder="المبلغ نقداً">
                                        </div>

                                        <div class="form-group d-none" id="cash-id">
                                            <label for="cash_amount">اختر الخزينة</label>
                                            <div class="input-group">
                                                <select name="cash_id" id="cash_id" class="form-control"
                                                    style="width: 100%"></select>
                                            </div>
                                        </div>
                                        <div class="form-group d-none" id="bank-input">
                                            <label for="bank_amount">المبلغ عبر البنك</label>
                                            <input type="number" name="bank_amount" id="bank_amount"
                                                class="form-control" min="0" placeholder="المبلغ بالبنك">
                                        </div>
                                        <div class="form-group d-none" id="bank-id">
                                            <label for="bank_amount"> اختر البنك</label>
                                            <select name="bank_id" id="bank_id" class="form-control"
                                                style="width: 100%"></select>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-start">
                                            <button type="button" class="btn btn-primary" id="confirm-payment">تأكيد
                                                وحفظ</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">إغلاق</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            $('select').select2();

            $('#payment_method').select2({
                dropdownParent: $('#paymentModal'),
                dir: 'rtl',
                placeholder: 'قم باختيار وسيلة الدفع'
            });

            $(document).on('click', '.remove-product', function() {
                let currentRow = $(this).closest('tr');
                currentRow.remove();
                totalPrice();
            });

            $('#product-rows').on('change', '.unit_id', function() {
                const unitSelect = $(this);
                let unitId = unitSelect.val();
                let productId = unitSelect.data('product-id');
                const currentRow = unitSelect.closest('tr');
                const qtyInput = currentRow.find('.qty');
                let oldPrice = currentRow.find('.price').val();

                $.ajax({
                    url: '{{ route('product.getUnitPrice') }}',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        unitId: unitId,
                        productId: productId
                    })
                }).done(function(data) {
                    if (data.success) {
                        const price = parseFloat(data.price) || 0;
                        const qty = parseFloat(qtyInput.val()) || 0;
                        const subTotal = (price * qty).toFixed(2);
                        currentRow.find('.price').val(price.toFixed(2));
                        currentRow.find('.row_sub_total').val(subTotal);
                        totalPrice();
                        checkQuantity(unitSelect, productId, qtyInput, data.firstPrice);

                    } else {
                        notif({
                            msg: data.message,
                            type: 'error',
                        });
                        resetSelect2(unitSelect, qtyInput, data.firstPrice);
                        totalPrice();
                    }
                }).fail(function() {
                    notif({
                        msg: ' حدث خطأ أثناء الاتصال بالسيرفر.',
                        type: 'error',
                    });
                });
            });

            $('#product-rows').on('blur', '.qty', function() {
                const currentRow = $(this).closest('tr');
                const price = parseFloat(currentRow.find('.price')
                    .val()) || 0;
                const qty = parseFloat($(this).val()) || 0;
                const subTotal = (price * qty).toFixed(2);
                let unitSelect = currentRow.find('.unit_id'); // تحديد الـ select
                let productId = currentRow.find('.product_id'); // قيمة المنتج
                let qtyInput = currentRow.find('.qty'); // حقل الكمية
                 $.ajax({
                    url: '{{ route('product.checkQty') }}',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        unit_id: unitSelect.val(),
                        product_id: productId.val(),
                        qty: qtyInput.val(),
                        stock_id: $('#stock_id').val()
                    })
                }).done(function(result) {
                    if (!result.success) {
                        notif({
                            msg: result
                                .message, // رسالة الخطأ
                            type: 'error',
                        });

                        let firstOption = unitSelect.find('option:first').val();
                        unitSelect.val(firstOption).trigger(
                            'change.select2'); // إعادة تعيين الـ select
                        qtyInput.val(1); // إعادة تعيين الكمية إلى 1
                        unitSelect.closest('tr').find('.price').val(result.product.units[0].pivot
                            .price);
                        unitSelect.closest('tr').find('.row_sub_total').val((result.product.units[0]
                            .pivot.price * 1).toFixed(2));
                        totalPrice();

                    } else {
                        currentRow.find('.row_sub_total').val(subTotal);
                        totalPrice();
                    }
                }).fail(function() {
                    notif({
                        msg: 'حدث خطأ أثناء الاتصال بالسيرفر.',
                        type: 'error',
                    });
                    let firstOption = unitSelect.find('option:first').val();
                    unitSelect.val(firstOption).trigger('change.select2'); // إعادة تعيين الـ select
                    qtyInput.val(1); // إعادة تعيين الكمية إلى 1
                    unitSelect.closest('tr').find('.price').val(parseFloat(oldPrice));
                    unitSelect.closest('tr').find('.row_sub_total').val((parseFloat(oldPrice) * 1)
                        .toFixed(2));
                    totalPrice();
                });
            });

            document.getElementById('discount').addEventListener('input', function() {
                totalPrice();
            });

            $('#invoiceForm').validate({
                rules: {
                    invoice_date: {
                        required: true,
                        date: true // تصحيح القاعدة بدلاً من `data: required`
                    },
                    invoice_number: {
                        required: true,
                        number: true
                    },
                    supplier_id: {
                        required: function(element) {
                            return document.getElementById('payment_status').value == 1;
                        },
                        number: true
                    },
                    payment_status: {
                        required: true,
                        number: true
                    },
                    sub_total: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    tax: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    discount: {
                        number: true,
                        min: 0
                    },
                    total: {
                        required: true,
                        number: true,
                        min: 0
                    },

                },
                messages: {
                    invoice_date: {
                        required: "يرجى إدخال تاريخ الفاتورة.",
                        date: "يرجى إدخال تاريخ صحيح."
                    },
                    invoice_number: {
                        required: "يرجى إدخال رقم الفاتورة.",
                        number: "يجب أن يكون رقم الفاتورة عدداً."
                    },
                    supplier_id: {
                        required: "يرجى إدخال اسم المورد.",
                        number: "يجب أن يكون اسم المورد عدداً."
                    },
                    payment_status: {
                        required: "يرجى إدخال حالة الفاتورة.",
                        number: "يجب أن تكون حالة الفاتورة عدداً."
                    },
                    sub_total: {
                        required: "يرجى إدخال الاجمالي قبل الضريبة للفاتورة.",
                        number: "يجب أن يكون الاجمالي قبل الضريبة للفاتورة عدداً.",
                        min: "يجب ان يكون اجمالي الضريبةاكبر من الصفراً."
                    },
                    tax: {
                        required: "يرجى إدخال قيمة الضريبة للفاتورة.",
                        number: "يجب أن تكون قيمة الضريبة للفاتورة عدداً.",
                        min: "يجب أن تكون قيمة الضريبة اكبر من الصفر."
                    },
                    discount: {
                        number: "يجب أن يكون خصم الفاتورة عدداً.",
                        min: "يجب أن يكون الخصم اكبر من الصفر."
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

            function addValidationRules() {
                $('input.product_name').each(function() {
                    $(this).rules("add", {
                        required: true,
                        messages: {
                            required: "يرجى ادخال اسم المنتج ."
                        }
                    });
                });

                $('select.unit_id').each(function() {
                    $(this).rules("add", {
                        required: true,
                        number: true,
                        messages: {
                            required: "يرجى اختيار اسم الوحدة.",
                            number: "يجب أن يكون اسم الوحدة رقمًا."
                        }
                    });
                });

                $('input.qty').each(function() {
                    $(this).rules("add", {
                        required: true,
                        number: true,
                        messages: {
                            required: "يرجى إدخال الكمية.",
                            number: "يجب أن تكون الكمية رقمًا."
                        }
                    });
                });

                $('input.price').each(function() {
                    $(this).rules("add", {
                        required: true,
                        number: true,
                        messages: {
                            required: "يرجى إدخال السعر.",
                            number: "يجب أن تكون السعر رقمًا."
                        }
                    });
                });

                $('input.row_sub_total').each(function() {
                    $(this).rules("add", {
                        required: true,
                        number: true,
                        messages: {
                            required: "يرجى إدخال الاجمالي قبل الضريبة.",
                            number: "يجب أن يكون الاجمالي قبل الضريبة رقمًا."
                        }
                    });
                });
            }

            // تطبيق القواعد عند تحميل الصفحة
            addValidationRules();


            // التحقق عند الإرسال
            $("#invoiceForm").on("submit", function(e) {
                if (!$("#invoiceForm").valid()) {
                    e.preventDefault();
                }
            });

            document.getElementById('save-invoice').addEventListener('click', function() {
                if ($('#invoiceForm').valid()) {
                    let paymentStatusVal = $('#payment_status').val();
                    if (paymentStatusVal == 1) {
                        $('#invoiceForm').submit();
                    } else {
                        $('#paymentModal').modal('show');
                    }
                }
            });

            $('#confirm-payment').on('click', function() {
                const paymentMethod = $('#payment_method').val();
                if (!paymentMethod) {
                    $('#payment_method').rules(
                        'add', {
                            required: true,
                            messages: {
                                required: "يرجى إدخال وسيلة الدفع"
                            }
                        });
                }


                $('#cash_name').rules(
                    'add', {
                        required: true,
                        messages: {
                            required: "يرجى ادخال اسم حساب الخزينة"
                        }
                    });

                $('#bank_name').rules(
                    'add', {
                        required: true,
                        messages: {
                            required: "يرجى إدخال اسم حساب البنك"
                        }
                    });

                $('#bank_id').rules(
                    'add', {
                        required: true,
                        messages: {
                            required: "يرجى إدخال اسم حساب البنك"
                        }
                    });

                $('#bank_amount').rules(
                    'add', {
                        required: true,
                        messages: {
                            required: "يرجى إدخال اسم حساب الخزينة"
                        }
                    });

                $('#cash_id').rules(
                    'add', {
                        required: true,
                        messages: {
                            required: "يرجى إدخال المبلغ بالكاش"
                        }
                    });

                $('#cash_amount').rules(
                    'add', {
                        required: true,
                        messages: {
                            required: "يرجى إدخال المبلغ بالبنك"
                        }
                    });

                $('#invoiceForm').submit();
            });

            $('#payment_method').on('change', function() {
                let selectedMethod = $(this).val();
                let selectMethod1 = selectedMethod;

                $('#cash-list, #bank-list, #cash-input, #bank-input, #cash-id, #bank-id').addClass(
                    'd-none');
                $('#cash_amount, #bank_amount').val('');
                $('#cash_name, #bank_name, #bank_id, #cash_id').val('').trigger('change');

                if (selectedMethod == '1') {
                    $('#cash-list').removeClass('d-none');
                } else if (selectedMethod === '2') {
                    $('#bank-list').removeClass('d-none');
                } else if (selectedMethod === '3') {
                    $('#cash-input, #bank-input, #cash-id, #bank-id').removeClass('d-none');
                }
            });
            select2list("#cash_name", "{{ route('sale_invoice.select2') }}", 1, 'قم باختيار حساب الخزينة');
            select2list("#bank_name", "{{ route('sale_invoice.select2') }}", 2, 'قم باختيار حساب الينك');
            select2list("#cash_id", "{{ route('sale_invoice.select2') }}", 1, 'قم باختيار حساب الخزينة');
            select2list("#bank_id", "{{ route('sale_invoice.select2') }}", 2, 'قم باختيار حساب البنك');
        });

        function checkQuantity(unitSelect, productId, qtyInput, oldPrice) {
            $.ajax({
                url: '{{ route('product.checkQty') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    unit_id: unitSelect.val(),
                    product_id: productId,
                    qty: qtyInput.val(),
                    stock_id: $('#stock_id').data('stock_id')
                }
            }).done(function(result) {
                if (!result.success) {
                    notif({
                        msg: result.message,
                        type: 'error',
                    });
                    resetSelect2(unitSelect, qtyInput, oldPrice);
                }
            }).fail(function() {
                notif({
                    msg: '⚠️ حدث خطأ أثناء الاتصال بالسيرفر.',
                    type: 'error',
                });
            });
        }

        function resetSelect2(unitSelect, qtyInput, oldPrice) {
            let firstOption = unitSelect.find('option:first').val();
            unitSelect.val(firstOption).trigger('change.select2'); // إعادة تعيين الـ select
            qtyInput.val(1); // إعادة تعيين الكمية إلى 1
            unitSelect.closest('tr').find('.price').val(parseFloat(oldPrice).toFixed(2));
            unitSelect.closest('tr').find('.row_sub_total').val((parseFloat(oldPrice) * 1).toFixed(2));
            totalPrice();
        }

        function totalPrice() {
            let subTotal = 0;
            document.querySelectorAll('.row_sub_total').forEach(function(selector) {
                let rowSubTotal = parseFloat(selector.value) || 0; // تحويل القيمة إلى رقم والتعامل مع القيم الفارغة
                subTotal += rowSubTotal; // جمع القيم
            });

            // تعيين القيمة النهائية مع التنسيق العشري
            let sub_total = parseFloat(subTotal); // تأكد من تحويله إلى رقم
            document.getElementById('sub_total').value = sub_total.toFixed(2);

            let discount = parseFloat(document.getElementById('discount').value) || 0;
            let tax = parseFloat(sub_total * 17 / 100) || 0;
            document.getElementById('tax').value = tax.toFixed(2);

            let total = parseFloat(sub_total + tax - discount);
            document.getElementById('total').value = total.toFixed(2);

            // 🔹 تأكد أن المقارنة مع رقم
            if (sub_total == 0) {
                document.getElementById('total').value = '';
            }
        }

        function select2list(selector, url, selectMethod, placeholder) {
            $(selector).select2({
                language: {
                    inputTooShort: function() {
                        return 'ادخل حرف واحد على الاقل';
                    }
                },
                dropdownParent: $('#paymentModal'),
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 100,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page,
                            selectMethod: selectMethod
                        };
                    },
                    processResults: function(data, params) {
                        console.log(data);
                        params.page = params.page || 1;
                        return {
                            results: data,
                            pagination: {
                                more: ((data.total_count) > (params.page * 20))
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                placeholder: placeholder
            });

        }
    </script>
@endsection
