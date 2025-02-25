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
                <h5 class="content-title mb-0 my-auto">إدارة فواتير المبدئية</h5>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ فاتورة مبدئية جديدة</span>
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
                {{-- <div class="card-header pb-0">
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
                                    msg: " تم حفط البيانات بنجاح",
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

                    <div class="col-sm-6 col-md-4 col-xl-3 mg-t-20">
                        @can('اضافة فاتورة مبدئية')
                            <a class="btn btn-primary btn-block" href="{{ route('PreliminaryInvoice.create') }}"><i
                                    class="fa fa-plus"> </i>&nbsp;&nbsp;إضافة فاتورة مبدئية
                                </i></a>
                        @endcan
                    </div>
                </div> --}}
                <div class="card-body">
                    <form id="invoiceForm" action="{{ route('preliminaryInvoice.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="invoice_date">تاريخ الفاتورة</label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                        value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="invoice_number">رقم الفاتورة</label>
                                    <input type="text" name="invoice_number" id="invoice_number" class="form-control"
                                        value="{{ $invoice_number }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="customer_id">العميل</label>
                                    <select name="customer_id" id="customer_id" class="form-control">
                                        <option value="">عميل كاش</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->tree4_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="stock_id">المخزن</label>
                                    <select name="stock_id" id="stock_id" class="form-control">
                                        @foreach ($stocks as $stock)
                                            <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row my-4">
                            <div class="col-md-12">
                                <input type="text" id="add-product" class="form-control"
                                    placeholder="قم بالبحث عن طريق الباركود أو الاسم">
                                <div id="error-message" class="error-message"></div>

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
                                <tbody id="product-rows"></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"></td>
                                        <th>الاجمالي قبل الضريبة</th>
                                        <td colspan="2"><input type="number" class="form-control sub_total"
                                                id="sub_total" name="sub_total" min="0" readonly></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <th>قيمة الضريبة(17%)</th>
                                        <td colspan="2"><input type="number" class="form-control tax" id="tax"
                                                name="tax" min="0" readonly></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <th>الخصم</th>
                                        <td colspan="2"><input type="number" class="form-control discount"
                                                id="discount" name="discount" step="0.01" value="0.00" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <th> الاجمالي النهائي</th>
                                        <td colspan="2"><input type="number" class="form-control total" id="total"
                                                name="total" min="0" readonly></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary" id="save-invoice">حفظ الفاتورة</button>
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

            document.getElementById('add-product').focus();
            $('select').select2();

            let debounceTimer;
            $('#add-product').on('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    const productName = $('#add-product').val().trim();
                    if (productName) {
                        fetch('{{ route('product.getProduct') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    name: productName,
                                    stock_id: $('#stock_id').val()
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const tbody = $('#product-rows');
                                    const rowCount = tbody.find('tr').length + 1;
                                    const unitIndex = rowCount;

                                    const row = `
                                        <tr>
                                            <td>${rowCount}</td>
                                            <td>
                                                <input type="text" id="product_name_${unitIndex}" name="product_name[${unitIndex}]" class="form-control product_name" value="${data.product.name}" readonly>
                                                <input type="hidden" id="product_id_${unitIndex}" name="product_id[${unitIndex}]" class="product_id" value="${data.product.id}">
                                            </td>
                                            <td>
                                                <select id="unit_id_${unitIndex}" class="form-control unit_id" name="unit_id[${unitIndex}]" data-product-id="${data.product.id}">
                                                    ${data.units.map(unit => `<option value="${unit.id}">${unit.name}</option>`).join('')}
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" id="qty_${unitIndex}" class="form-control qty" name="qty[${unitIndex}]" step="1" min="1" value="1">
                                            </td>
                                            <td>
                                                <input type="number" id="price_${unitIndex}" class="form-control price" name="price[${unitIndex}]" step="0.01" min="0" value="${data.product.units[0].pivot.price}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" id="row_sub_total_${unitIndex}" class="form-control row_sub_total" name="row_sub_total[${unitIndex}]" step="0.01" value="${data.product.units[0].pivot.price}" readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-product"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    `;
                                    $.ajax({
                                        url: '{{ route('product.checkQty') }}',
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        data: JSON.stringify({
                                            unit_id: data.units[0].pivot
                                                .unit_id,
                                            product_id: data.product.id,
                                            qty: 1,
                                            stock_id: $('#stock_id').val(),
                                        })
                                    }).done(function(result) {
                                        if (result.success) {
                                            tbody.append(row);
                                            $('.unit_id').select2();
                                            $('#add-product').val('');
                                            $(`#product_name_${unitIndex}`).rules(
                                                'add', {
                                                    required: true,
                                                    messages: {
                                                        required: "يرجى إدخال اسم المنتج"
                                                    }
                                                });

                                            $(`#unit_id_${unitIndex}`).rules('add', {
                                                required: true,
                                                messages: {
                                                    required: "يرجى اختيار الوحدة"
                                                }
                                            });

                                            $(`#qty_${unitIndex}`).rules('add', {
                                                required: true,
                                                number: true,
                                                min: 1,
                                                messages: {
                                                    required: "يرجى إدخال الكمية",
                                                    number: "الكمية يجب أن تكون رقمًا",
                                                    min: "يجب أن تكون الكمية 1 أو أكثر"
                                                }
                                            });

                                            $(`#price_${unitIndex}`).rules('add', {
                                                required: true,
                                                number: true,
                                                min: 0,
                                                messages: {
                                                    required: "يرجى إدخال السعر",
                                                    number: "السعر يجب أن يكون رقمًا",
                                                    min: "يجب أن يكون السعر 0 أو أكثر"
                                                }
                                            });

                                            $(`#row_sub_total_${unitIndex}`).rules(
                                                'add', {
                                                    required: true,
                                                    number: true,
                                                    messages: {
                                                        required: "يرجى إدخال المجموع الفرعي",
                                                        number: "المجموع الفرعي يجب أن يكون رقمًا"
                                                    }
                                                });
                                            totalPrice();
                                        } else {
                                            notif({
                                                msg: result.message,
                                                type: 'error'
                                            });
                                        }
                                    }).fail(function() {
                                        notif({
                                            msg: "حدث خطأ أثناء الاتصال بالسيرفر.",
                                            type: 'error'
                                        });
                                    });

                                } else {
                                    notif({
                                        msg: data.message,
                                        type: 'error'
                                    });
                                }
                            })
                            .catch(error => {
                                notif({
                                    msg: 'حدث خطأ ما، يرجى المحاولة لاحقًا',
                                    type: 'error'
                                });
                            });
                    }
                }, 500);
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
                    customer_id: {
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
                    customer_id: {
                        required: "يرجى إدخال اسم العميل.",
                        number: "يجب أن يكون اسم العميل عدداً."
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

        });


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

        // ✅ دالة التحقق من الكمية بعد تغيير الوحدة أو الكمية
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
                    stock_id: $('#stock_id').val()
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
    </script>
@endsection
