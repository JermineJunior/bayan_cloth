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
                <h5 class="content-title mb-0 my-auto">إدارة المخازن </h5>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ التحويل بين المخازن</span>
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
                <div class="card-body">
                    <form id="transformForm" action="{{ route('stock.transform.store') }}" method="POST">
                        @csrf
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
                                        <th>اسم المنتج</th>
                                        <th>التحويل من </th>
                                        <th>التحويل الى</th>
                                        <th>الوحدة</th>
                                        <th>الكمية</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="product-rows"></tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary" id="save-invoice"> حفظ</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
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
                                    stock_id: null,                                 
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
                                                <select id="stock_id_from_${unitIndex}" class="form-control stock_id_from" name="stock_id_from[${unitIndex}]" >
                                                    ${data.stock_from.map(stock => `<option value="${stock.id}">${stock.name}</option>`).join('')}
                                                </select>                                             
                                            </td>

                                            <td>
                                                <select id="stock_id_to_${unitIndex}" class="form-control stock_id_to" name="stock_id_to[${unitIndex}]" >
                                                    ${data.stocks.map(stock => `<option value="${stock.id}">${stock.name}</option>`).join('')}
                                                </select>                                            
                                            </td>

                                            <td>
                                                <input type="number" id="qty_${unitIndex}" class="form-control qty" name="qty[${unitIndex}]" step="1" min="1" value="1">
                                            </td>

                                            <td>
                                                <select id="unit_id_${unitIndex}" class="form-control unit_id" name="unit_id[${unitIndex}]" data-product-id="${data.product.id}">
                                                    ${data.units.map(unit => `<option value="${unit.id}">${unit.name}</option>`).join('')}
                                                </select>
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
                                            stock_id: data.stock_from[0].id,
                                            
                                        })
                                    }).done(function(result) {
                                        if (result.success) {
                                            tbody.append(row);
                                            $('.stock_id_from').select2();
                                            $('.stock_id_to').select2();
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

                                            $(`#stock_id_from_${unitIndex}`).rules(
                                                'add', {
                                                    required: true,
                                                    number: true,
                                                    min: 0,
                                                    messages: {
                                                        required: "يرجى إدخال السعر",
                                                        number: "السعر يجب أن يكون رقمًا",
                                                        min: "يجب أن يكون السعر 0 أو أكثر"
                                                    }
                                                });

                                            $(`#stock_id_to_${unitIndex}`).rules(
                                                'add', {
                                                    required: true,
                                                    number: true,
                                                    messages: {
                                                        required: "يرجى إدخال المجموع الفرعي",
                                                        number: "المجموع الفرعي يجب أن يكون رقمًا"
                                                    }
                                                });
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
                                    msg: 'حدث خطأ ما يرجى المحاولة مرة اخرى',
                                    type: 'error'
                                });
                            });
                    }
                }, 500);
            });

            $(document).on('click', '.remove-product', function() {
                let currentRow = $(this).closest('tr');
                currentRow.remove();
            });

            $('#product-rows').on('change', '.unit_id', function() {
                const unitSelect = $(this);
                let unitId = unitSelect.val();
                let productId = unitSelect.data('product-id');
                const currentRow = unitSelect.closest('tr');
                let qtyInput = currentRow.find('.qty');
                let stockId = currentRow.find('.stock_id_from');
                checkQuantity(unitSelect, productId, qtyInput, stockId);
            });

            $('#product-rows').on('change', '.stock_id_from', function() {
                const unitStock = $(this);
                const currentRow = unitStock.closest('tr');
                let stockId = unitStock.val();
                let unitId = currentRow.find('.unit_id');
                let productId = unitId.data('product-id');
                let qtyInput = currentRow.find('.qty');
                checkQuantity(unitStock, productId, qtyInput, unitId);
            });


            $('#product-rows').on('blur', '.qty', function() {
                const currentRow = $(this).closest('tr');
                let qty = parseFloat($(this).val()) || 0;
                let unitSelect = currentRow.find('.unit_id'); // تحديد الـ select
                let productId = currentRow.find('.product_id'); // قيمة المنتج
                let qtyInput = currentRow.find('.qty');
                let stockId = currentRow.find('.stock_id_from')
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
                        stock_id: stockId.val()
                    })
                }).done(function(result) {
                    if (!result.success) {
                        notif({
                            msg: result
                                .message, // رسالة الخطأ
                            type: 'error',
                        });

                        let firstOption = stockId.find('option:first').val();
                        stockId.val(firstOption).trigger(
                            'change.select2'); // إعادة تعيين الـ select
                        qtyInput.val(1); // إعادة تعيين الكمية إلى 1

                    }
                }).fail(function() {
                    notif({
                        msg: 'حدث خطأ أثناء الاتصال بالسيرفر.',
                        type: 'error',
                    });
                    let firstOption = stockId.find('option:first').val();
                    stockId.val(firstOption).trigger('change.select2'); // إعادة تعيين الـ select
                    qtyInput.val(1); // إعادة تعيين الكمية إلى 1
                });

            });

            $('#transformForm').validate({
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

        // ✅ دالة التحقق من الكمية بعد تغيير الوحدة أو الكمية
        function checkQuantity(unitSelect, productId, qtyInput, stockId) {
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
                    stock_id: stockId.val()
                }
            }).done(function(result) {
                if (!result.success) {
                    notif({
                        msg: result.message,
                        type: 'error',
                    });
                    resetSelect2(stockId, qtyInput);
                }
            }).fail(function() {
                notif({
                    msg: '⚠️ حدث خطأ أثناء الاتصال بالسيرفر.',
                    type: 'error',
                });
            });
        }

        function resetSelect2(stockId, qtyInput) {
            let firstOption = stockId.find('option:first').val();
            stockId.val(firstOption).trigger('change.select2'); // إعادة تعيين الـ select
            qtyInput.val(1); // إعادة تعيين الكمية إلى 1
        }
    </script>
@endsection
