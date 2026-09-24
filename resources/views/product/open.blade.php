@extends('layouts.app')

@section('css')
    <!-- Internal Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

{{-- @section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto">إدارة فواتير المشتريات</h5>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ فاتورة مشتريات جديدة</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection --}}

@section('content')
    <form id="invoiceForm" action="{{ route('product.opening.store') }}" method="POST">
        @csrf
        <div class="row row-sm">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <input type="text" name="search" id="search" class="form-control search"
                            placeholder="قم بالبحث هنا ">
                        <div class="table-responsive">
                            <table class="table  table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>المنتج</th>
                                        {{-- <th>الكمية</th> --}}
                                        {{-- <th>السعر</th> --}}
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="body_search">
                                    @foreach ($products as $product)
                                        <tr>
                                            <th>{{ $loop->iteration }}</th>
                                            <th>{{ $product->name }}</th>
                                            {{-- <th>{{ $product->convertQuantity($product->stocks()->first()->pivot->quantity) }}
                                            </th> --}}
                                            {{-- <th>{{ number_format($product->units()->first()->pivot->price_purchase) . ' لل' . $product->units()->first()->name }}
                                            </th> --}}
                                            <th><a href="#" class="btn btn-primary btn-add-invoice"
                                                    data-id="{{ $product->id }}"><i class="fa fa-plus"></i></a>
                                            </th>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <input type="text" id="add-product" class="form-control mb-4"
                            placeholder="قم بالبحث عن طريق الباركود أو الاسم">
                        <div id="error-message" class="error-message"></div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="wd-25p">الاسم</th>
                                        <th class="wd-15p">الوحدة</th>
                                        <th class="wd-15p">الكمية</th> <!-- صغير -->
                                        <th class="wd-30p">المخزن</th> <!-- أكبر -->
                                        <th>حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="product-rows">
                                    @foreach ($stock_details as $stock_detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="text" id="product_name_{{ $loop->iteration }}"
                                                    name="product_name[{{ $loop->iteration }}]"
                                                    class="form-control product_name"
                                                    value="{{ $stock_detail->product->name }}" readonly>
                                                <input type="hidden" id="product_id_{{ $loop->iteration }}"
                                                    name="product_id[{{ $loop->iteration }}]" class="product_id"
                                                    value="{{ $stock_detail->product_id }}">
                                            </td>
                                            <td>
                                                <select id="unit_id_{{ $loop->iteration }}" class="form-control unit_id"
                                                    name="unit_id[{{ $loop->iteration }}]"
                                                    data-product-id="{{ $stock_detail->product_id }}"
                                                    data-unit_id="{{ $stock_detail->unit_id }}">
                                                    @foreach ($stock_detail->product->units as $unit)
                                                        <option @selected($unit->id == $stock_detail->unit_id) value="{{ $unit->id }}">
                                                            {{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="qty_{{ $loop->iteration }}"
                                                    class="form-control qty" name="qty[{{ $loop->iteration }}]"
                                                    value="{{ number_format($stock_detail->qty) }}"
                                                    data-qty="{{ $stock_detail->qty }}">
                                            </td>
                                            <td>
                                                <select id="stock_id_{{ $loop->iteration }}" class="form-control stock_id"
                                                    name="stock_id[{{ $loop->iteration }}]"
                                                    data-stock_id="{{ $stock_detail->stock_id }}">
                                                    @foreach ($stocks as $stock)
                                                        <option @selected($stock->id == $stock_detail->stock_id) value="{{ $stock->id }}">
                                                            {{ $stock->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-product"><i
                                                        class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary mb-4" id="save-invoice">حفظ</button>

                                <a href="javascript:history.back()" class="btn btn-warning mb-4">
                                    <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                                </a>
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
            let deletedProducts = [];

            document.getElementById('add-product').focus();
            $('select').select2();
            $('#cash_name').select2();
            $('#bank_name').select2();
            $('#cash_id').select2();
            $('#bank_id').select2();
            let addedProducts = [];

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
                                    stock_id: $('#stock_id').val(),
                                    type: 1,
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
                                                <input type="text" id="qty_${unitIndex}" class="form-control qty" name="qty[${unitIndex}]"  value="1">
                                            </td>
                                            <td>
                                                <select id="stock_id_${unitIndex}" class="form-control stock_id" name="stock_id[${unitIndex}]">
                                                    ${data.stocks.map(stock => `<option value="${stock.id}">${stock.name}</option>`).join('')}
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-product"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    `;
                                    tbody.append(row);
                                    attachCommaFormatter(`#price_${unitIndex}`);
                                    attachCommaFormatter(`#qty_${unitIndex}`);
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

                                    $(`#qty_${unitIndex}`).rules('add', {
                                        required: true,
                                        messages: {
                                            required: "يرجى إدخال الكمية",
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
                let productId = currentRow.find('.product_id').val();
                let originalQuantity = currentRow.find('.qty').data('qty');
                let stockId = currentRow.find('.stock_id').data('stock_id');
                let unitId = currentRow.find('.unit_id').data('unit_id');
                addedProducts = addedProducts.filter(id => id !== productId);
                deletedProducts.push({
                    id: productId,
                    qty: originalQuantity,
                    stockId: stockId,
                    unitId: unitId,
                });
                currentRow.remove();
            });

            $('#product-rows').on('change', '.unit_id', function() {
                const unitSelect = $(this);
                let unitId = unitSelect.val();
                let productId = unitSelect.data('product-id');
                const currentRow = unitSelect.closest('tr');
                const qtyInput = currentRow.find('.qty');
                let stockId = currentRow.find('.stock_id').val();
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
                        stockId: stockId
                    })
                }).done(function(data) {
                    if (data.success) {
                        const price_purchase = parseFloat(data.price_purchase) || 0;
                        const qty = parseFloat(qtyInput.val().replace(/,/g, '')) || 0;
                        const subTotal = (price_purchase * qty);
                        currentRow.find('.price').val(Number(price_purchase).toLocaleString(
                            'en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                        currentRow.find('.row_sub_total').val(Number(subTotal).toLocaleString(
                            'en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                    } else {
                        notif({
                            msg: data.message,
                            type: 'error',
                        });
                    }
                }).fail(function() {
                    notif({
                        msg: ' حدث خطأ أثناء الاتصال بالسيرفر.',
                        type: 'error',
                    });
                });
            });


            $('#product-rows').on('blur', '.qty', function() {
                let currentRow = $(this).closest('tr');

                const unformat = str => parseFloat(String(str).replace(/,/g, '')) || 0;
                const format = num => num.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                let price = unformat(currentRow.find('.price').val()) || 0;
                let qty = unformat($(this).val()) || 0;
                let subTotal = (price * qty).toFixed(2);
                currentRow.find('.row_sub_total').val(Number(subTotal).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            });


            $('#invoiceForm').validate({

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
                },
                submitHandler: function(form) {
                    cleanNumbersBeforeSubmit();
                    updateProductQty(deletedProducts);
                    form.submit();
                }
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
                            addedProducts.push(productId);

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
                                        <input type="text" id="qty_${unitIndex}" class="form-control qty" name="qty[${unitIndex}]" value="1">
                                    </td>
                                    <td>
                                        <select id="stock_id_${unitIndex}" class="form-control stock_id" name="stock_id[${unitIndex}]">
                                            ${data.stocks.map(stock => `<option value="${stock.id}">${stock.name}</option>`).join('')}
                                        </select>                                    
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger remove-product"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                            attachCommaFormatter(`#price_${unitIndex}`);
                            attachCommaFormatter(`#qty_${unitIndex}`);
                            $('.unit_id').select2();
                            $('.stock_id').select2();

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
                                    required: "يرجى إدخال الكمية",
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

            $('#search').on('keyup', function() {
                $.ajax({
                    url: '{{ route('sale_invoice.search') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        search: $('#search').val(),
                        type: 3,
                        // stock_id: $('#stock_id').val(),
                    },
                    success: function(result) {
                        let tbody = $('#body_search');
                        tbody.empty();
                        let rows = '';
                        result.products.forEach((product, index) => {
                            if (product.quantity == 0) return;

                            rows += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${product.name}</td>
                                        <td>
                                            <a href="#" class="btn btn-primary btn-add-invoice" data-id="${product.id}">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </td>
                                    </tr>
                                `;
                        });

                        tbody.append(rows);
                    }
                })
            });



            attachCommaFormatter('#cash_amount_total');
            attachCommaFormatter('#bank_amount_total');
            attachCommaFormatter('#deferend_amount');
            attachCommaFormatter('#discount');

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
        }

        function updateProductQty(deletedProducts) {
            if (deletedProducts.length > 0) {
                $.ajax({
                    url: '{{ route('product.updateQty') }}',
                    type: "POST",
                    data: {
                        deleted_products: deletedProducts,
                        _token: '{{ csrf_token() }}',
                        stock_id: $('#stock_id').data('stock_id'),
                    },
                    success: function(response) {
                        deletedProducts = []; // مسح المصفوفة بعد الحفظ
                    },
                    error: function(xhr) {
                        notif({
                            type: 'error',
                            message: 'حدث خطأ أثنا�� تحديث الكمية'
                        });
                    }
                });
            }
        }
    </script>
@endsection
