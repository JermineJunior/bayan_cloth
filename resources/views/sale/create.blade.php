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
    @include('includes.invoice-type-indicator', ['type' => 'sale'])
@endsection

@section('content')

    <!-- row opened -->

    <form id="invoiceForm" action="{{ route('sale_invoice.store') }}" method="POST">
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
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date">تاريخ الفاتورة</label>
                                    <input type="date" name="date" id="date" class="form-control"
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
                                    <div class="d-flex align-items-start gap-1">
                                        {{-- زر الإضافة --}}
                                        <a class="modal-effect btn btn-outline-info" data-target="#customerModal"
                                            data-toggle="modal" href="#customerModal" data-id=""
                                            title="اضافة عميل جديد">
                                            <i class="fa fa-plus"></i>
                                        </a>

                                        {{-- الحقل داخل div عشان يتحكم فيه بالعرض الكامل --}}
                                        <div class="flex-grow-1">
                                            <select name="customer_id" id="customer_id" class="form-control select2">
                                                <option value="">اختر اسم العميل</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->tree4_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            {{-- خطأ التحقق تحت السلكت فقط --}}
                                            @error('customer_id')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="representative_id">المندوب</label>
                                    <div class="d-flex">
                                        <a class="modal-effect btn btn-outline-info" data-target="#representativeModal"
                                            data-toggle="modal" href="#representativeModal" data-id=""
                                            title="اضافة مندوب جديد">
                                            <i class="fa fa-plus">
                                            </i></a>
                                        <select name="representative_id" id="representative_id" class="form-control">
                                            <option value="">اختر اسم المندوب</option>
                                            @foreach ($representatives as $representative)
                                                <option value="{{ $representative->id }}">
                                                    {{ $representative->tree4_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-12 col-xl-3">
                <div class="card">
                    <div class="card-body scrollable-card">
                        <select id="product_filter" class="form-control mb-2">
                            <option value="0">كل المنتجات</option>
                            <option value="1">المتاح فقط</option>
                        </select>
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

            <div class="col-12 col-xl-9">
                <div class="card invoice-card">
                    <div class="card-body invoice-body">

                        <input type="text" id="add-product" class="form-control mb-2"
                            placeholder="قم بالبحث عن طريق الباركود أو اسم المنتج">

                        <div id="error-message" class="error-message"></div>

                        <!-- ====== منطقة المنتجات فقط ====== -->
                        <div class="table-responsive invoice-products scrollable-card">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>المخزن</th>
                                        <th>ك بالمخزن</th>
                                        <th>الوحدة</th>
                                        <th>الكمية</th>
                                        <th>البونص</th>
                                        <th>تاريخ الصلاحية</th>
                                        <th>السعر</th>
                                        <th>الإجمالي</th>
                                        <th>حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="product-rows"></tbody>
                            </table>
                        </div>

                        <!-- ====== منطقة الحسابات (ثابتة دائماً) ====== -->
                        <div class="invoice-summary">

                            <input type="hidden" name="sub_total" class="form-control" id="sub_total" readonly>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label>
                                        قيمة الضريبة (17%)
                                        <input type="checkbox" name="tax_status" id="apply_tax"> تطبيق
                                    </label>
                                    <input type="text" name="tax" class="form-control" id="tax"
                                        value="0.00" readonly>
                                </div>

                                <div class="col-md-2">
                                    <label>الخصم</label>
                                    <input type="text" name="discount" class="form-control" id="discount"
                                        value="0.00">
                                </div>
                                <div class="col-md-7">
                                    <label>الإجمالي النهائي</label>
                                    <input type="text" name="total" class="form-control" id="total" readonly>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-5"></div>
                                <div class="col-md-7">
                                    <label>كاش</label>
                                    <div class="form-row-inline">
                                        <select id="cash_name" name="cash_name" class="form-control">
                                            @foreach ($safes as $safe)
                                                <option value="{{ $safe->id }}">{{ $safe->tree4_name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="cash_amount_total" id="cash_amount_total"
                                            class="form-control" placeholder="المبلغ نقداً">
                                    </div>
                                </div>

                                <div class="col-md-5"></div>
                                <div class="col-md-7">
                                    <label>بنك</label>
                                    <div class="form-row-inline">
                                        <select id="bank_name" name="bank_name" class="form-control">
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->tree4_name }} -
                                                    {{ $bank->no_bank }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="bank_amount_total" id="bank_amount_total"
                                            class="form-control" placeholder="المبلغ بالبنك">
                                        <input type="text" name="transaction_no" id="transaction_no"
                                            class="form-control" placeholder="رقم العملية" style="display:none;">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="deferend_amount" name="deferend_amount" class="form-control"
                                value="0.00" readonly>

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

    <div class="modal" id="customerModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">إضافة عميل جديد </h6><button aria-label="Close" class="close"
                        data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="addCustomer" action="#" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <span class="tx-danger">*</span><label for="exampleInputEmail1">إسم العميل </label>
                            <input type="text" name="name" id="name" class="form-control"
                                autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">البريد الالكتروني للعميل
                            </label>
                            <input type="text" name="email" id="email" class="form-control"
                                autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">رقم هاتف العميل </label>
                            <input type="number" name="phone" id="phone" class="form-control"
                                autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">عنوان العميل </label>
                            <input type="text" name="address" id="address" class="form-control"
                                autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">اسم المندوب </label>
                            <select name="representative_id" id="representative_id_customer" class="form-control">
                                <option value="">قم باختيار مندوب</option>
                                @foreach ($representatives as $representative)
                                    <option value="{{ $representative->id }}">{{ $representative->tree4_name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="btnAddCustomer">إضافة </button>
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">إغلاق</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="representativeModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">إضافة مندوب جديد </h6><button aria-label="Close" class="close"
                        data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="addRepresentative" action="#" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <span class="tx-danger">*</span><label for="exampleInputEmail1">إسم المندوب </label>
                            <input type="text" name="name_representative" id="name_representative"
                                class="form-control" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">البريد الالكتروني للعميل
                            </label>
                            <input type="text" name="email_representative" id="email_representative"
                                class="form-control" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">رقم هاتف المندوب </label>
                            <input type="number" name="phone_representative" id="phone_representative"
                                class="form-control" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">عنوان المندوب </label>
                            <input type="text" name="address_representative" id="address_representative"
                                class="form-control" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>نسبة المندوب %</label>
                            <input type="number" name="percentage_representative" id="percentage_representative"
                                class="form-control" min="0" max="100" step="0.01" value="0" autocomplete="off">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="btnAddRepresentative">إضافة </button>
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">إغلاق</button>
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
            $('select').select2({
                'width': '100%',
            });
            let addedProducts = [];
            let addedProductsDelete = [];
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
                                    if (addedProducts.includes(data.product.id)) {
                                        return
                                    }
                                    addedProducts.push(data.product.id);
                                    const tbody = $('#product-rows');
                                    const rowCount = tbody.find('tr').length + 1;
                                    const unitIndex = rowCount;

                                    const row = `
                                        <tr>
                                            <td>
                                                <input type="text" id="product_name_${unitIndex}" style="width:200px; text-align:left" name="product_name[${unitIndex}]" class="form-control product_name" value="${data.product.name}" readonly>
                                                <input type="hidden" id="product_id_${unitIndex}" name="product_id[${unitIndex}]" class="product_id" value="${data.product.id}">
                                            </td>
                                            <td>
                                                <select id="stock_id_${unitIndex}" class="form-control stock_id" style="width:140px" name="stock_id[${unitIndex}]" data-product-id="${data.product.id}">
                                                    ${data.stocks.map(stock => `<option value="${stock.id}">${stock.name}</option>`).join('')}
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="qty_in_stock_${unitIndex}" class="form-control qty_in_stock" style="width:50px" name="qty_in_stock_[${unitIndex}]" disabled value="${data.qty_in_stock}">
                                            </td>
                                            <td>
                                                <select id="unit_id_${unitIndex}" class="form-control unit_id" style="width:70px" name="unit_id[${unitIndex}]" data-product-id="${data.product.id}">
                                                    ${data.units.map(unit => `<option value="${unit.id}">${unit.name}</option>`).join('')}
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="qty_${unitIndex}" class="form-control qty" style="width:50px" name="qty[${unitIndex}]" value="1">
                                            </td>
                                            <td>
                                                <input type="text" id="bounce_${unitIndex}" class="form-control bounce" style="width:50px" name="bounce[${unitIndex}]" value="0">
                                            </td>
                                            <td>
                                                <input type="date" name="expire_date[${unitIndex}]" id="expire_date${unitIndex}" class="form-control" value="${data.product.expire_date}">
                                            </td>
                                            <td>
                                            <input type="text" id="price_${unitIndex}" class="form-control price" style="width:90px" name="price[${unitIndex}]" value="${Number(data.product.units[0].pivot.price).toLocaleString('en-US', {minimumFractionDigits: 2,maximumFractionDigits: 2})}">
                                            </td>
                                            <td>
                                                <input type="text" id="row_sub_total_${unitIndex}" class="form-control row_sub_total" style="width:100px" name="row_sub_total[${unitIndex}]" value="${Number(data.product.units[0].pivot.price).toLocaleString('en-US', {minimumFractionDigits: 2,maximumFractionDigits: 2})}" readonly>
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
                                    attachCommaFormatter(`#price_${unitIndex}`);
                                    $('.btn-add-invoice').each(function(index,
                                        button) {
                                        const $btn = $(
                                            button
                                        ); // تحويل العنصر إلى jQuery object

                                        if ($btn.data('id') == data
                                            .product.id) {
                                            $btn.addClass(
                                                'btn-white disabled'
                                            ).removeClass(
                                                'btn-primary');
                                        }
                                    });
                                    $('.unit_id').select2();
                                    $('.stock_id').select2();
                                    $('#add-product').val('');
                                    $(`#product_name_${unitIndex}`).rules(
                                        'add', {
                                            required: true,
                                            messages: {
                                                required: "يرجى إدخال اسم المنتج"
                                            }
                                        });

                                    $(`#stock_id_${unitIndex}`).rules('add', {
                                        required: true,
                                        messages: {
                                            required: "يرجى اختيار المخزن"
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
                                            required: "يرجى إدخال الكمية",
                                        }
                                    });

                                    $(`#price_${unitIndex}`).rules('add', {
                                        required: true,

                                        messages: {
                                            required: "يرجى إدخال السعر"
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

                                    if (flagZeroPrice($('#product-rows tr').first()[0])) {
                                        notif({
                                            msg: 'هذا المنتج ليس له سعر بيع محدد',
                                            type: 'warning'
                                        });
                                    }

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
                flagZeroPrice(closestRow[0]);
            });

            $(document).on('click', '.remove-product', function() {
                let currentRow = $(this).closest('tr');

                let productId = Number(currentRow.find('.product_id').val());

                addedProductsDelete = addedProductsDelete.filter(id => Number(id) !== productId);

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

            $('#product-rows').on('change', '.unit_id', function() {
                const unitSelect = $(this);
                let unitId = unitSelect.val();
                let productId = unitSelect.data('product-id');
                const currentRow = unitSelect.closest('tr');
                const qtyInput = currentRow.find('.qty');
                const qtyBounce = currentRow.find('.bounce');
                let stockId = currentRow.find('.stock_id').val();
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
                        productId: productId,
                        stockId: stockId,
                    })
                }).done(function(data) {
                    if (data.success) {
                        const price = parseFloat(data.price) || 0;
                        const qty = parseFloat(qtyInput.val().replace(/,/g, '')) || 0;
                        const subTotal = (price * qty);
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

                        if (flagZeroPrice(currentRow[0])) {
                            notif({
                                msg: 'هذا المنتج ليس له سعر بيع محدد',
                                type: 'warning'
                            });
                        }
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

            $('#product-rows').on('change', '.stock_id', function() {
                const stockSelect = $(this);
                let stockId = stockSelect.val();
                const currentRow = stockSelect.closest('tr');
                let productId = currentRow.find('.product_id').val();
                let unitId = currentRow.find('.unit_id').val();
                $.ajax({
                    url: '{{ route('product.getUnitPrice') }}',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        unitId: unitId,
                        productId: productId,
                        stockId: stockId,
                    })
                }).done(function(data) {
                    if (data.success) {
                        $('.qty_in_stock').val(data.qty_in_stock);
                    }
                });
            });


            $('#product-rows').on('blur', '.qty', function() {
                const currentRow = $(this).closest('tr');

                const unformat = str => parseFloat(String(str).replace(/,/g, '')) || 0;
                const format = num => num.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                const price = unformat(currentRow.find('.price').val());
                const qty = unformat($(this).val()); // لو الكمية تُعرض بفواصل لاحقًا
                const subTotal = price * qty;

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

            document.getElementById('discount').addEventListener('input', function() {
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
                        required: function(element) {
                            return document.getElementById('deferend_amount').value != '0.00' ||
                                document.getElementById('representative_id').value != '';
                        },
                        number: true
                    },
                    sub_total: {
                        required: true
                    },
                    tax: {
                        required: true
                    },
                    total: {
                        required: true,

                    },
                    deferend_amount: {
                        required: true,
                        nonNegativeDefer: true
                    },
                    cash_amount_total: {
                        requiredCashNameIfAmount: true
                    },
                    bank_amount_total: {
                        requiredBankNameIfAmount: true
                    },
                    transaction_no: {
                        requiredTransactionNoIfBankAmount: true
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
                        required: "يرجى إدخال الاجمالي قبل الضريبة للفاتورة."
                    },
                    tax: {
                        required: "يرجى إدخال قيمة الضريبة للفاتورة."
                    },
                    total: {
                        required: "يرجى إدخال الاجمالي للفاتورة."
                    },
                    transaction_no: {
                        requiredTransactionNoIfBankAmount: "يرجى إدخال رقم عملية البنك"
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.closest('.input-group').length) {
                        error.insertAfter(element.closest('.input-group'));
                    } else if (element.hasClass('select2-hidden-accessible')) {
                        // أضف رسالة الخطأ بعد الغلاف الأساسي لـ Select2
                        error.insertAfter(element.next('span.select2'));
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
                $.validator.addMethod("requiredCashNameIfAmount", function(value, element) {
                    const cashAmount = ($('#cash_amount_total').val() || '').trim();
                    const cashName = ($('#cash_name').val() || '').trim();

                    if (cashAmount !== '' && cashName === '') {
                        return false;
                    }

                    return true;
                }, "يرجى إدخال اسم الخزينة إذا قمت بإدخال المبلغ نقداً.");


                $.validator.addMethod("requiredBankNameIfAmount", function(value, element) {
                    const bankAmount = ($('#bank_amount_total').val() || '').trim();
                    const bankName = ($('#bank_name').val() || '').trim();

                    if (bankAmount !== '' && bankName === '') {
                        return false;
                    }

                    return true;
                }, "يرجى إدخال اسم البنك إذا قمت بإدخال المبلغ بالبنك.");


                $.validator.addMethod("requiredTransactionNoIfBankAmount", function(value, element) {
                    const unformat = v => parseFloat(String(v).replace(/,/g, '')) || 0;
                    const bank = unformat($('#bank_amount_total').val());
                    if (bank > 0) {
                        return value != null && String(value).trim() !== '';
                    }
                    return true;
                }, "يرجى إدخال رقم عملية البنك");


                $.validator.addMethod(
                    "nonNegativeDefer",
                    function(value, element) {
                        const unformat = v => parseFloat(String(v).replace(/,/g, '')) || 0;
                        const defer = unformat(value);
                        return defer >= 0;
                    },
                    "المبلغ الآجل لا يمكن أن يكون أقل من الصفر"
                );

                $.validator.addMethod("uniqueEmailCustomer", function(value, element) {
                    let isValid = false;
                    if (isValid == '') {
                        return isValid = true;
                    }
                    $.ajax({
                        url: '{{ route('customer.checkEmail') }}',
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

                $.validator.addMethod("uniquePhoneCustomer", function(value, element) {
                    let isValid = false;
                    if (isValid == '') {
                        return isValid = true;
                    }
                    $.ajax({
                        url: '{{ route('customer.checkPhone') }}',
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
                }, "هذا رقم العميل موجود بالفعل في النظام.");

                $.validator.addMethod("uniqueNameCustomer", function(value, element) {
                    let isValid = false;
                    $.ajax({
                        url: '{{ route('customer.checkName') }}',
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
                }, "هذا الاسم  موجود بالفعل في النظام.");

                $('#addCustomer').validate({
                    rules: {
                        name: {
                            required: true,
                            uniqueNameCustomer: true,
                        },
                        email: {
                            uniqueEmailCustomer: true,
                            email: true,
                        },
                        phone: {
                            uniquePhoneCustomer: true,
                            number: true
                        },
                        representative_id: {
                            required: true
                        }
                    },
                    messages: {
                        name: {
                            required: "يرجى إدخال اسم العميل.",
                        },
                        email: {
                            email: "الرجاء إدخال بريد إلكتروني صالح.",
                        },
                        phone: {
                            number: "يجب أن يكون رقم العميل عدداً."
                        },
                        representative_id: {
                            required: "يجب اختيار اسم المندوب"
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
                            url: '{{ route('add.customer') }}',
                            data: $(form).serialize(),
                            success: function(response) {
                                if (response.success === true) {
                                    notif({
                                        msg: 'تم إضافة العميل بنجاح',
                                        type: 'success'
                                    });

                                    // إخفاء المودال (تأكد من أن مودالك يحتوي على هذا ID)
                                    $('#customerModal').modal('hide');

                                    // إعادة تعيين الفورم
                                    form.reset();

                                    var newOption = new Option(response.newCustomer
                                        .name, response.newCustomer.id, true,
                                        true);
                                    $('#customer_id').append(newOption).trigger(
                                        'change');

                                    var select = $('#representative_id');
                                    var value = String(response.representative.id);
                                    var text = response.representative.name;

                                    // لو المندوب غير موجود ضيفه
                                    if (select.find('option[value="' + value + '"]')
                                        .length === 0) {
                                        select.append(new Option(text, value, true,
                                            true));
                                    }

                                    // اعمل select
                                    select.val(value).trigger('change');

                                } else {
                                    notif({
                                        msg: ' لم يتم إضافة العميل',
                                        type: 'error'
                                    });
                                }
                            },

                        });
                    }
                });

                // ربط الزر مع الفورم
                $('#btnAddCustomer').on('click', function() {
                    $('#addCustomer').submit();
                });

                $('#customer_id').on('change', function() {

                    var customerId = $(this).val();
                    var url = "{{ route('customer.representative', ':id') }}";
                    url = url.replace(':id', customerId);
                    if (!customerId) return;

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {

                            if (response.success) {

                                var select = $('#representative_id');
                                var value = String(response.representative.id);
                                var text = response.representative.name;

                                // لو المندوب غير موجود ضيفه
                                if (select.find('option[value="' + value + '"]')
                                    .length === 0) {
                                    select.append(new Option(text, value, true, true));
                                }

                                // اعمل select
                                select.val(value).trigger('change');
                            }
                        }
                    });
                });


                $.validator.addMethod("uniqueEmail", function(value, element) {
                    let isValid = false;
                    if (isValid == '') {
                        return isValid = true;
                    }
                    $.ajax({
                        url: '{{ route('representative.checkEmail') }}',
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

                $.validator.addMethod("uniquePhone", function(value, element) {
                    let isValid = false;
                    if (isValid == '') {
                        return isValid = true;
                    }
                    $.ajax({
                        url: '{{ route('representative.checkPhone') }}',
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
                }, "هذا رقم المندوب موجود بالفعل في النظام.");

                $.validator.addMethod("uniqueName", function(value, element) {
                    let isValid = false;
                    $.ajax({
                        url: '{{ route('representative.checkName') }}',
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
                }, "هذا الاسم موجود بالفعل في النظام.");

                $('#addRepresentative').validate({
                    rules: {
                        name_representative: {
                            required: true,
                            uniqueName: true,
                        },
                        email_representative: {
                            uniqueEmail: true,
                            email: true,
                        },
                        phone_representative: {
                            uniquePhone: true,
                            number: true
                        },
                        percentage_representative: {
                            number: true,
                            min: 0,
                            max: 100
                        },
                    },
                    messages: {
                        name_representative: {
                            required: "يرجى إدخال اسم المندوب.",
                        },
                        email_representative: {
                            email: "الرجاء إدخال بريد إلكتروني صالح.",
                        },
                        phone_representative: {
                            number: "يجب أن يكون رقم المندوب عدداً."
                        },
                        percentage_representative: {
                            number: "يرجى إدخال نسبة المندوب كرقم.",
                            min: "يجب أن تكون نسبة المندوب 0 على الأقل.",
                            max: "يجب ألا تتجاوز نسبة المندوب 100."
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
                    },
                    submitHandler: function(form) {
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('add.representative') }}',
                            data: {
                                name: $('#name_representative').val(),
                                email: $('#email_representative').val(),
                                phone: $('#phone_representative')
                                    .val(), // ← بدون علامات تنصيص
                                address: $('#address_representative').val(),
                                rep_percentage: $('#percentage_representative').val() !== '' ? $(
                                        '#percentage_representative').val() : 0,
                                _token: '{{ csrf_token() }}'

                            },

                            success: function(response) {
                                if (response.success === true) {
                                    notif({
                                        msg: 'تم إضافة المندوب بنجاح',
                                        type: 'success'
                                    });

                                    // إخفاء المودال (تأكد من أن مودالك يحتوي على هذا ID)
                                    $('#representativeModal').modal('hide');

                                    // إعادة تعيين الفورم
                                    form.reset();

                                    var newOption = new Option(response
                                        .newRepresentative
                                        .name, response.newRepresentative.id,
                                        true,
                                        true);
                                    $('#representative_id').append(newOption)
                                        .trigger(
                                            'change');
                                } else {
                                    notif({
                                        msg: ' لم يتم إضافة المندوب',
                                        type: 'error'
                                    });
                                }
                            },

                        });
                    }
                });

                // ربط الزر مع الفورم
                $('#btnAddRepresentative').on('click', function() {
                    $('#addRepresentative').submit();
                });
            });

            $(document).on('click', '.btn-add-invoice', function(e) {
                e.preventDefault();

                const button = $(this);
                const productId = button.data('id');

                // ✅ تحقق من القائمة المؤقتة بدل الـ DOM
                if (addedProducts.includes(productId)) {
                    return;
                }
                addedProducts.push(productId);
                button.prop('disabled', true);
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
                                        <input type="text" id="product_name_${unitIndex}" style="width:200px; text-align:left" name="product_name[${unitIndex}]" class="form-control product_name" value="${data.product.name}" readonly>
                                        <input type="hidden" id="product_id_${unitIndex}" name="product_id[${unitIndex}]" class="product_id" value="${data.product.id}">
                                    </td>
                                    <td>
                                        <select id="stock_id_${unitIndex}" class="form-control stock_id" style="width:140px" name="stock_id[${unitIndex}]" data-product-id="${data.product.id}">
                                            ${data.stocks.map(stock => `<option value="${stock.id}">${stock.name}</option>`).join('')}
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" id="qty_in_stock_${unitIndex}" class="form-control qty_in_stock" style="width:50px" name="qty_in_stock_[${unitIndex}]" disabled value="${data.qty_in_stock}">
                                    </td>
                                    <td>
                                        <select id="unit_id_${unitIndex}" class="form-control unit_id" style="width:70px" name="unit_id[${unitIndex}]" data-product-id="${data.product.id}">
                                            ${Array.isArray(data.units) ? data.units.map(unit => `<option value="${unit.id}">${unit.name}</option>`).join('') : ''}
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" id="qty_${unitIndex}" class="form-control qty" style="width:50px" name="qty[${unitIndex}]" value="1">
                                    </td>
                                    <td>
                                        <input type="text" id="bounce_${unitIndex}" class="form-control bounce" style="width:50px" name="bounce[${unitIndex}]" value="0">
                                    </td>
                                    <td>
                                        <input type="date" name="expire_date[${unitIndex}]" id="expire_date${unitIndex}" class="form-control" value="${data.product.expire_date}">
                                    </td>
                                    <td>
                                        <input type="text" id="price_${unitIndex}" class="form-control price" style="width:90px" name="price[${unitIndex}]" value="${Number(data.product.units[0].pivot.price).toLocaleString('en-US', {minimumFractionDigits: 2,maximumFractionDigits: 2})}">
                                    </td>
                                    <td>
                                        <input type="text" id="row_sub_total_${unitIndex}" class="form-control row_sub_total" style="width:100px" name="row_sub_total[${unitIndex}]" value="${Number(data.product.units[0].pivot.price).toLocaleString('en-US', {minimumFractionDigits: 2,maximumFractionDigits: 2})}" readonly>
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
                            attachCommaFormatter(`#price_${unitIndex}`);
                            $('.btn-add-invoice').each(function(index,
                                button) {
                                const $btn = $(
                                    button
                                ); // تحويل العنصر إلى jQuery object

                                if ($btn.data('id') == data
                                    .product.id) {
                                    $btn.addClass(
                                        'btn-white disabled'
                                    ).removeClass(
                                        'btn-primary');
                                }
                            });
                            $('.unit_id').select2();
                            $('.stock_id').select2();
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

                            $(`#stock_id${unitIndex}`).rules('add', {
                                required: true,
                                messages: {
                                    required: "يرجى اختيار المخزن"
                                }
                            });

                            $(`#qty_${unitIndex}`).rules('add', {
                                required: true,
                                messages: {
                                    required: "يرجى إدخال الكمية",
                                }
                            });

                            // $(`#bounce_${unitIndex}`).rules('add', {
                            //     required: true,
                            //     messages: {
                            //         required: "يجب ان يكون البونص على الاقل يساوي الصفر كاقل قيمة ممكنة",
                            //     }
                            // });

                            $(`#price_${unitIndex}`).rules('add', {
                                required: true,

                                messages: {
                                    required: "يرجى إدخال السعر"
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

                            if (flagZeroPrice($('#product-rows tr').first()[0])) {
                                notif({
                                    msg: 'هذا المنتج ليس له سعر بيع محدد',
                                    type: 'warning'
                                });
                            }

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

            function loadProducts() {
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
                            available: $('#product_filter').val(),
                        },
                        success: function(result) {
                            let tbody = $('#body_search');
                            tbody.empty();
                            let rows = '';
                            (result.products || []).forEach((product, index) => {
                                rows += `
                                    <a href="#" class="product-pick-item product-pick-item--grad btn btn-primary btn-add-invoice" data-id="${product.id}">${product.name}</a>
                                `;
                            });

                            if (!rows) {
                                tbody.append('<span class="text-muted small">لا توجد منتجات</span>');
                                return;
                            }

                            tbody.append(rows);
                        }
                    })
                }, 300);
            }

            $('#search').on('keyup', loadProducts);
            $('#product_filter').on('change', loadProducts);

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

            $('#cash_amount_total').on('input', function() {
                totalDeferend();
            });

            $('#bank_amount_total').on('input', function() {
                totalDeferend();
                const unformat = v => parseFloat(String(v).replace(/,/g, '')) || 0;
                const $tx = $('#transaction_no');
                if (unformat($(this).val()) > 0) {
                    $tx.show();
                } else {
                    $tx.hide();
                    $tx.val('');
                }
            });

            attachCommaFormatter('#cash_amount_total');
            attachCommaFormatter(
                '#bank_amount_total');
            attachCommaFormatter('#deferend_amount');
            attachCommaFormatter('#discount');

            document.getElementById('invoiceForm').addEventListener('submit', function(e) {
                const zeroPriceNames = getZeroPriceRows();
                if (zeroPriceNames.length) {
                    const msg = 'هذه المنتجات ليس لها سعر بيع محدد:\n' +
                        zeroPriceNames.join('\n') +
                        '\n\nلا يزال بإمكانك الحفظ، هل تريد المتابعة؟';
                    if (!confirm(msg)) {
                        e.preventDefault();
                        return;
                    }
                }
                cleanNumbersBeforeSubmit();
            });


        });

        /* ـــ الدوال المساعدة للتحذير من الأسعار غير المحددة (0) ـــ */

        function isZeroPriceValue(value) {
            const clean = String(value).replace(/,/g, '').trim();
            return clean === '' || isNaN(parseFloat(clean)) || parseFloat(clean) === 0;
        }

        function flagZeroPrice(row) {
            if (!row) return false;

            const priceInput = row.querySelector('.price');
            if (!priceInput) return false;

            const zero = isZeroPriceValue(priceInput.value);
            priceInput.classList.toggle('border-warning', zero);
            priceInput.classList.toggle('zero-price', zero);

            let hint = row.querySelector('.zero-price-hint');
            if (zero) {
                if (!hint) {
                    hint = document.createElement('small');
                    hint.className = 'zero-price-hint text-warning d-block';
                    hint.textContent = 'السعر غير محدد';
                    priceInput.parentNode.appendChild(hint);
                }
            } else if (hint) {
                hint.remove();
            }

            return zero;
        }

        function getZeroPriceRows() {
            const names = [];
            document.querySelectorAll('#product-rows tr').forEach(row => {
                const priceInput = row.querySelector('.price');
                const nameInput = row.querySelector('.product_name');
                if (priceInput && isZeroPriceValue(priceInput.value)) {
                    names.push(nameInput && nameInput.value ? nameInput.value : 'منتج بدون اسم');
                }
            });
            return names;
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

        function totalPrice() {
            const unformat = str => parseFloat(String(str).replace(/,/g, '')) || 0;
            const format = num => num.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            let subTotal = 0;
            document.querySelectorAll('.row_sub_total').forEach(function(selector) {
                let rowSubTotal = unformat(selector.value) || 0; // تحويل القيمة إلى رقم والتعامل مع القيم الفارغة
                subTotal += rowSubTotal; // جمع القيم
            });

            // تعيين القيمة النهائية مع التنسيق العشري
            let sub_total = unformat(subTotal); // تأكد من تحويله إلى رقم
            document.getElementById('sub_total').value = format(sub_total);

            let discount = unformat(document.getElementById('discount').value) || 0;
            let tax = $('#apply_tax').is(':checked') ? unformat(sub_total * 17 / 100) || 0 : 0;
            document.getElementById('tax').value = format(tax);

            let cash = unformat(document.getElementById('cash_amount_total').value) || 0;
            let bank = unformat(document.getElementById('bank_amount_total').value) || 0;

            let total = unformat(sub_total + tax - discount);
            document.getElementById('total').value = format(total);
            document.getElementById('deferend_amount').value = format(total - cash - bank);

            // 🔹 تأكد أن المقارنة مع رقم
            if (sub_total == 0) {
                document.getElementById('total').value = '';
                document.getElementById('deferend_amount').value = '0.00';
            }
        }

        function totalDeferend() {
            const unformat = str => parseFloat(String(str).replace(/,/g, '')) || 0;
            const format = num => num.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            let total = unformat($('#total').val());
            let cash = unformat($('#cash_amount_total').val());
            let bank = unformat($('#bank_amount_total').val());

            $('#deferend_amount').val(format(total - cash - bank))
        }


        function resetSelect2(unitSelect, qtyInput, oldPrice) {
            let firstOption = unitSelect.find('option:first').val();
            unitSelect.val(firstOption).trigger('change.select2'); // إعادة تعيين الـ select
            qtyInput.val(1); // إعادة تعيين الكمية إلى 1
            qtyBounce.val(0);
            unitSelect.closest('tr').find('.price').val(parseFloat(oldPrice).toFixed(2));
            unitSelect.closest('tr').find('.row_sub_total').val((parseFloat(oldPrice) * 1).toFixed(2));
            totalPrice();
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

            // نفس الشيء للحقول الفردية:
            let singleFields = ['cash_amount_total', 'bank_amount_total', 'discount', 'sub_total', 'tax', 'total',
                'deferend_amount'
            ];
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
    </script>
@endsection
