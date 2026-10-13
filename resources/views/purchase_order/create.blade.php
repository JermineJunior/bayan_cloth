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

@section('content')
    <form id="invoiceForm" action="{{ route('purchase_order.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">تاريخ الفاتورة</label>
                                    <input type="date" name="date" id="date" class="form-control"
                                        value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_id">المورد</label>
                                    <div class="d-flex align-items-start gap-1">
                                        {{-- زر الإضافة --}}
                                        <a class="modal-effect btn btn-outline-info" data-target="#supplierModal"
                                            data-toggle="modal" href="#supplierModal" data-id=""
                                            title="اضافة مورد جديد">
                                            <i class="fa fa-plus"></i>
                                        </a>

                                        {{-- الحقل داخل div عشان يتحكم فيه بالعرض الكامل --}}
                                        <div class="flex-grow-1">
                                            <select name="supplier_id" id="supplier_id" class="form-control select2">
                                                <option value="">-- اختر اسم المورد --</option>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->tree4_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            {{-- خطأ التحقق تحت السلكت فقط --}}
                                            @error('supplier_id')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card">
                    <div class="card-body scrollable-card">
                        <input type="text" name="search" id="search" class="form-control search"
                            placeholder="قم بالبحث هنا ">
                        <div id="body_search">
                            @foreach ($products as $product)
                                <a href="#" class="product-pick-item product-pick-item--grad btn btn-primary btn-add-invoice" data-id="{{ $product->id }}">{{ $product->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="card invoice-card">
                    <div class="card-body invoice-body">
                        <input type="text" id="add-product" class="form-control mb-4"
                            placeholder="قم بالبحث عن طريق الباركود أو الاسم">
                        <div class="table-responsive invoice-products scrollable-card">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>الوحدة</th>
                                        <th>الكمية</th> <!-- صغير -->
                                        <th>حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="product-rows"></tbody>
                            </table>
                        </div>
                        <div class="invoice-summary">
                            <div class="text-center mt-1">
                                <button type="submit" class="btn btn-primary">حفظ الفاتورة</button>
                                <a href="javascript:history.back()" class="btn btn-warning">رجوع</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
    </div>
    </div>
    <div class="modal" id="supplierModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">إضافة مورد جديد </h6><button aria-label="Close" class="close"
                        data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="addSupplier" action="#" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <span class="tx-danger">*</span><label for="exampleInputEmail1">إسم المورد </label>
                            <input type="text" name="name" id="name" class="form-control"
                                autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">البريد الالكتروني للمورد
                            </label>
                            <input type="text" name="email" id="email" class="form-control"
                                autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">رقم هاتف المورد </label>
                            <input type="number" name="phone" id="phone" class="form-control"
                                autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">عنوان المورد </label>
                            <input type="text" name="address" id="address" class="form-control"
                                autocomplete="off">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn ripple btn-primary" id="btnAddSupplier">إضافة </button>
                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">إغلاق</button>
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
        document.addEventListener('DOMContentLoaded', function() {

            document.getElementById('add-product').focus();
            $('select').select2();
            let addedProducts = [];

            let debounceTimer;
            function lookupProduct(showError) {
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
                                    type: 1
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if ($('#add-product').val().trim() !== productName) {
                                    return;
                                }
                                if (data.success) {
                                    const tbody = $('#product-rows');
                                    const rowCount = tbody.find('tr').length + 1;
                                    const unitIndex = rowCount;

                                    const row = `
                                        <tr>
                                            <td>
                                                <input type="text" id="product_name_${unitIndex}" style="width:250px" name="product_name[${unitIndex}]" class="form-control product_name" value="${data.product.name}" readonly>
                                                <input type="hidden" id="product_id_${unitIndex}" name="product_id[${unitIndex}]" class="product_id" value="${data.product.id}">
                                            </td>
                                            <td>
                                                <select id="unit_id_${unitIndex}" class="form-control unit_id" style="width:200px" name="unit_id[${unitIndex}]" data-product-id="${data.product.id}">
                                                    ${data.units.map(unit => `<option value="${unit.id}">${unit.name}</option>`).join('')}
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="qty_${unitIndex}" class="form-control qty" style="width:50px" name="qty[${unitIndex}]" value="1">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-product"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    `;
                                    tbody.prepend(row);
                                    attachCommaFormatter(`#qty_${unitIndex}`);
                                    attachCommaFormatter(
                                        `#bounce_${unitIndex}`);
                                    $('.btn-add-invoice').each(function(index,
                                        button) {
                                        const $btn = $(
                                            button
                                        ); // تحويل العنصر إلى jQuery object

                                        if ($btn.data('id') == result
                                            .product.id) {
                                            $btn.addClass(
                                                'btn-white disabled'
                                            ).removeClass(
                                                'btn-primary');
                                        }
                                    });
                                    $('.unit_id').select2();
                                    $('stock_id').select2();
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
                                        messages: {
                                            required: "يرجى إدخال الكمية"
                                        }
                                    });

                                    $(`#stock_id_${unitIndex}`).rules('add', {
                                        required: true,
                                        messages: {
                                            required: "يرجى اختيار المخزن"
                                        }
                                    });

                                    $(`#price_${unitIndex}`).rules('add', {
                                        required: true,
                                        messages: {
                                            required: "يرجى إدخال السعر",
                                        }
                                    });

                                    $(`#row_sub_total_${unitIndex}`).rules(
                                        'add', {
                                            required: true,
                                            messages: {
                                                required: "يرجى إدخال المجموع الفرعي",
                                            }
                                        });
                                    totalPrice();
                                } else if (showError) {
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
            }

            $('#add-product').on('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    lookupProduct(false);
                }, 700);
            });

            $('#add-product').on('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    clearTimeout(debounceTimer);
                    lookupProduct(true);
                }
            });

            $(document).on('click', '.remove-product', function() {
                let currentRow = $(this).closest('tr');

                let productId = Number(currentRow.find('.product_id').val());

                addedProducts = addedProducts.filter(id => Number(id) !== productId);

                currentRow.remove();

                $('.btn-add-invoice').each(function() {
                    const $btn = $(this);
                    if (Number($btn.data('id')) === productId) {
                        $btn.removeClass('btn-white disabled')
                            .addClass('btn-primary')
                            .prop('disabled', false);
                    }
                });

                totalPrice();
            });

            $(document).on('input', '.price', function() {
                const $this = $(this);
                const closestRow = $this.closest('tr');
                let priceVal = parseFloat($this.val().replace(/,/g, ''));
                let qtyVal = parseFloat(closestRow.find('.qty').val().replace(/,/g, ''));
                let subTotal = (priceVal * qtyVal);

                closestRow.find('.row_sub_total').val(Number(subTotal).toLocaleString(
                    'en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                totalPrice();
            });

            $('#product-rows').on('change', '.unit_id', function() {
                const unitSelect = $(this);
                let unitId = unitSelect.val();
                let productId = unitSelect.data('product-id');
                const currentRow = unitSelect.closest('tr');
                const qtyInput = currentRow.find('.qty');
                const qtyBounce = currentRow.find('.bounce');
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
                        const qty = parseFloat(qtyInput.val().replace(/,/g, '')) || 0;
                        const subTotal = (price * qty).toFixed(2);
                        currentRow.find('.price').val(Number(price).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        currentRow.find('.row_sub_total').val(Number(subTotal).toLocaleString(
                            'en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                        totalPrice();
                    } else {
                        notif({
                            msg: data.message,
                            type: 'error',
                        });
                        resetSelect2(unitSelect, qtyInput, qtyBounce, data.firstPrice);
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

                const unformat = str => parseFloat(String(str).replace(/,/g, '')) || 0;
                const format = num => num.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                const price = unformat(currentRow.find('.price')
                    .val()) || 0;
                const qty = unformat($(this).val()) || 0;
                const subTotal = (price * qty);
                let unitSelect = currentRow.find('.unit_id'); // تحديد الـ select
                let productId = currentRow.find('.product_id'); // قيمة المنتج
                let qtyInput = currentRow.find('.qty'); // حقل الكمية
                let qtyBounce = currentRow.find('.bounce');
                currentRow.find('.row_sub_total').val(Number(subTotal).toLocaleString(
                    'en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                totalPrice();

            });

            $('#invoiceForm').validate({
                rules: {
                    date: {
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
                        required: true
                    },
                    tax: {
                        required: true
                    },
                    total: {
                        required: true
                    },

                },
                messages: {
                    date: {
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
                    },
                    tax: {
                        required: "يرجى إدخال قيمة الضريبة للفاتورة.",
                    },
                    total: {
                        required: "يرجى إدخال الاجمالي للفاتورة.",
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

            $(document).ready(function() {
                $.validator.addMethod("uniqueEmailsupplier", function(value, element) {
                    let isValid = false;
                    if (isValid == '') {
                        return isValid = true;
                    }
                    $.ajax({
                        url: '{{ route('supplier.checkEmail') }}',
                        data: {
                            email: value
                        },
                        type: 'GET',
                        async: false, // تأكد من أن التحقق يتم بشكل متزامن
                        success: function(response) {
                            isValid = response.isUnique;
                        }
                    });
                    return isValid;
                }, "هذا البريد الالكتروني موجود بالفعل في النظام.");

                $.validator.addMethod("uniquephonesupplier", function(value, element) {
                    let isValid = false;
                    if (isValid == '') {
                        return isValid = true;
                    }
                    $.ajax({
                        url: '{{ route('supplier.checkPhone') }}',
                        data: {
                            phone: value
                        },
                        type: 'GET',
                        async: false, // تأكد من أن التحقق يتم بشكل متزامن
                        success: function(response) {
                            isValid = response.isUnique;
                        }
                    });
                    return isValid;
                }, "هذا رقم المورد موجود بالفعل في النظام.");

                $.validator.addMethod("uniqueNameSupplier", function(value, element) {
                    let isValid = false;
                    $.ajax({
                        url: '{{ route('supplier.checkName') }}',
                        data: {
                            name: value
                        },
                        type: 'GET',
                        async: false, // تأكد من أن التحقق يتم بشكل متزامن
                        success: function(response) {
                            isValid = response.isUnique;
                        }
                    });
                    return isValid;
                }, "هذا اسم المورد موجود بالفعل في النظام.");

                $('#addSupplier').validate({
                    rules: {
                        name: {
                            required: true,
                            uniqueNameSupplier: true,
                        },
                        email: {
                            uniqueEmailsupplier: true,
                            email: true,
                        },
                        phone: {
                            uniquephonesupplier: true,
                            number: true
                        }
                    },
                    messages: {
                        name: {
                            required: "يرجى إدخال اسم المورد.",
                        },
                        email: {
                            email: "الرجاء إدخال بريد إلكتروني صالح.",
                        },
                        phone: {
                            required: "يرجى إدخال رقم المورد.",
                        }
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
                    },
                    submitHandler: function(form) {
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('add.supplier') }}',
                            data: $(form).serialize(),
                            success: function(response) {
                                if (response.success === true) {
                                    notif({
                                        msg: 'تم إضافة العميل بنجاح',
                                        type: 'success'
                                    });

                                    // إخفاء المودال (تأكد من أن مودالك يحتوي على هذا ID)
                                    $('#supplierModal').modal('hide');

                                    // إعادة تعيين الفورم
                                    form.reset();

                                    var newOption = new Option(response.newSupplier
                                        .name, response.newSupplier.id, true,
                                        true);
                                    $('#supplier_id').append(newOption).trigger(
                                        'change');
                                }
                            },

                        });
                    }
                });

                // ربط الزر مع الفورم
                $('#btnAddSupplier').on('click', function() {
                    $('#addSupplier').submit();
                });

            });

            $(document).on('click', '.btn-add-invoice', function(e) {
                e.preventDefault();

                const button = $(this);
                const productId = button.data('id');

                $.ajax({
                    url: '{{ route('invoice.getProduct') }}',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        product_id: button.data('id'),
                        stock_id: $('#stock_id').val()
                    }),
                    success(data) {
                        if (data.success) {
                            const tbody = $('#product-rows');
                            const rowCount = tbody.find('tr').length + 1;
                            const unitIndex = rowCount;

                            const row = `
                                        <tr>
                                            <td>
                                                <input type="text" id="product_name_${unitIndex}" style="width:300px" name="product_name[${unitIndex}]" class="form-control product_name" value="${data.product.name}" readonly>
                                                <input type="hidden" id="product_id_${unitIndex}" name="product_id[${unitIndex}]" class="product_id" value="${data.product.id}">
                                            </td>
                                            <td>
                                                <select id="unit_id_${unitIndex}" class="form-control unit_id" style="width:200px" name="unit_id[${unitIndex}]" data-product-id="${data.product.id}">
                                                    ${data.units.map(unit => `<option value="${unit.id}">${unit.name}</option>`).join('')}
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="qty_${unitIndex}" class="form-control qty" style="width:50px" name="qty[${unitIndex}]" value="1">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-product"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    `;
                            tbody.prepend(row);
                            attachCommaFormatter(`#qty_${unitIndex}`);
                            attachCommaFormatter(`#bounce_${unitIndex}`);
                            $('.unit_id').select2();
                            $('.stock_id').select2();
                            button.addClass('btn-white disabled').removeClass(
                                'btn-primary');

                            $(`#product_name_${unitIndex}`).rules('add', {
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
                                messages: {
                                    required: "يرجى إدخال الكمية"
                                }
                            });

                            $(`#stock_id_${unitIndex}`).rules('add', {
                                required: true,
                                messages: {
                                    required: "يرجى اختيار المخزن"
                                }
                            });

                            $(`#price_${unitIndex}`).rules('add', {
                                required: true,
                                messages: {
                                    required: "يرجى إدخال السعر",
                                }
                            });

                            $(`#row_sub_total_${unitIndex}`).rules('add', {
                                required: true,
                                messages: {
                                    required: "يرجى إدخال المجموع الفرعي",
                                }
                            });

                            totalPrice();

                        } else {
                            notif({
                                msg: data.message,
                                type: 'error'
                            });
                        }
                    },
                    error() {
                        notif({
                            msg: 'حدث خطأ ما، يرجى المحاولة لاحقًا',
                            type: 'error'
                        });
                    }
                });
            });

            let searchDebounceTimer;
            let searchXhr;

            $('#search').on('keyup', function() {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    if (searchXhr) {
                        searchXhr.abort();
                    }
                    searchXhr = $.ajax({
                        url: '{{ route('sale_invoice.search') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            search: $('#search').val(),
                            type: 3,
                        },
                        success: function(result) {
                            let tbody = $('#body_search');
                            tbody.empty();
                            let rows = '';
                            result.products.forEach((product, index) => {
                                rows += `
                                    <a href="#" class="product-pick-item product-pick-item--grad btn btn-primary btn-add-invoice" data-id="${product.id}">${product.name}</a>
                                `;
                            });

                            tbody.append(rows);
                        }
                    })
                }, 300);
            });

            $('#apply_tax').on('change', function() {
                if ($(this).is(':checked')) {
                    // فعل الحقل وخليه يقبل القيمة
                    totalPrice();
                } else {
                    // عطل الحقل وخلي القيمة بصفر
                    $('#tax').val('0.00');
                    totalPrice();
                }
            });

            attachCommaFormatter('#discount');
            attachCommaFormatter('.qty');


            document.getElementById('invoiceForm').addEventListener('submit', function(e) {
                cleanNumbersBeforeSubmit();
            });

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

        function totalPrice() {
            const unformat = str => parseFloat(String(str).replace(/,/g, '')) || 0;
            const format = num => num.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            let subTotal = 0;
            document.querySelectorAll('.row_sub_total').forEach(function(selector) {
                let rowSubTotal = unformat(selector.value) ||
                    0; // تحويل القيمة إلى رقم والتعامل مع القيم الفارغة
                subTotal += rowSubTotal; // جمع القيم
            });

            // تعيين القيمة النهائية مع التنسيق العشري
            let sub_total = unformat(subTotal); // تأكد من تحويله إلى رقم
            document.getElementById('sub_total').value = format(sub_total);

            let discount = unformat(document.getElementById('discount').value) || 0;
            let tax = $('#apply_tax').is(':checked') ? unformat(sub_total * 17 / 100) || 0 : 0;
            document.getElementById('tax').value = format(tax);

            let total = unformat(sub_total + tax - discount);
            document.getElementById('total').value = format(total);
            document.getElementById('deferend_amount').value = format(total);

            // 🔹 تأكد أن المقارنة مع رقم
            if (sub_total == 0) {
                document.getElementById('total').value = '';
                document.getElementById('deferend_amount').value = '0.00';
            }
        }

        function cleanNumbersBeforeSubmit() {
            // الحقول ذات الأسماء المتكررة كمصفوفات (price[], row_sub_total[], qty[] ... الخ)
            // نفترض ان الفورم يحتوي على هذه الحقول، نمر عليها كلها
            document.querySelectorAll('input[name^="price"]').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
            document.querySelectorAll('input[name^="row_sub_total"]').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
            document.querySelectorAll('input[name^="qty"]').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
            document.querySelectorAll('input[name^="bounce"]').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });

            let singleFields = ['cash_amount_total', 'bank_amount_total', 'discount', 'sub_total', 'tax', 'total'];
            singleFields.forEach(id => {
                let el = document.getElementById(id);
                if (el && el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
        }

        function toggleSidebarBasedOnScreen() {
            if (window.innerWidth >= 768) {
                document.body.classList.add('sidenav-toggled');
            } else {
                document.body.classList.remove('sidenav-toggled');
            }
        }

        window.addEventListener('load', toggleSidebarBasedOnScreen);
        window.addEventListener('resize', toggleSidebarBasedOnScreen);

        function resetSelect2(unitSelect, qtyInput, qtyBounce, oldPrice) {
            let firstOption = unitSelect.find('option:first').val();
            unitSelect.val(firstOption).trigger('change.select2'); // إعادة تعيين الـ select
            qtyInput.val(1); // إعادة تعيين الكمية إلى 1
            qtyBounce.val(0);
            unitSelect.closest('tr').find('.price').val(parseFloat(oldPrice).toFixed(2));
            unitSelect.closest('tr').find('.row_sub_total').val((parseFloat(oldPrice) * 1).toFixed(2));
            totalPrice();
        }
    </script>
@endsection
