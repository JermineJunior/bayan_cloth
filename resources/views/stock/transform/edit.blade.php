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
    <div class="card">
        <div class="card-header pb-0">
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
            @if (session()->has('error'))
                <script>
                    window.onload = function() {
                        notif({
                            msg: " الكمية المدخلة في احد المنتجات اكبر من الكمية الموجودة في المخزن",
                            type: "error"
                        })
                    }
                </script>
            @endif

            <div class="col-sm-6 col-md-4 col-xl-12 mg-t-20">
                <div class="d-flex justify-content-between float-left">
                    <a href="javascript:history.back()" class="btn btn-warning">
                        <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="transformForm" action="{{ route('stock.transform.update',$transform->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="stock_id_from">المخزن المحول منه</label>
                                        <select name="stock_id_from" id="stock_id_from" class="form-control stock_id_from">
                                            @foreach ($stocks_from as $stock)
                                                <option @selected($stock->id == $transform->stock_from) value="{{ $stock->id }}">
                                                    {{ $stock->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="stock_id_to">المخزن المحول اليه</label>
                                        <select name="stock_id_to" id="stock_id_to" class="form-control stock_id_to">
                                            @foreach ($stocks_to as $stock)
                                                <option @selected($stock->id == $transform->stock_to) value="{{ $stock->id }}">
                                                    {{ $stock->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-body">
                                <input type="text" name="search" id="search" class="form-control search"
                                    placeholder="قم بالبحث هنا ">
                                <div id="body_search">
                                    @foreach ($stocks_from[0]->products as $product)
                                        @if ($product->pivot->quantity == 0)
                                        @continue
                                        @endif
                                        <a href="#" class="product-pick-item product-pick-item--grad btn btn-add-invoice btn-primary" data-id="{{ $product->id }}">{{ $product->name }} <span class="product-pick-qty">{{ $product->convertQuantity($product->pivot->quantity) }}</span></a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="card">
                            <div class="card-body">
                                <input type="text" id="add-product" class="form-control mb-4"
                                    placeholder="قم بالبحث عن طريق الباركود أو الاسم">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>اسم المنتج</th>
                                                {{-- <th class="wd-20p border-bottom-0">التحويل من </th>
                                                <th class="wd-20p border-bottom-0">التحويل الى</th> --}}
                                                <th>الكمية</th>
                                                <th>الوحدة</th>
                                                <th>حذف</th>
                                            </tr>
                                        </thead>
                                        <tbody id="product-rows">
                                            @foreach ($transform->transformDetails as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <input type="text" id="product_name_{{ $loop->index }}"
                                                            name="product_name[{{ $loop->index }}]"
                                                            class="form-control product_name" value="{{ $item->product->name }}"
                                                            readonly>
                                                        <input type="hidden" id="product_id_{{ $loop->index }}"
                                                            name="product_id[{{ $loop->index }}]" class="product_id"
                                                            value="{{ $item->product_id }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" id="qty_{{ $loop->index }}" class="form-control qty"
                                                            name="qty[{{ $loop->index }}]" step="1" min="1"
                                                            value="{{ $item->quantity }}">
                                                    </td>

                                                    <td>
                                                        <select id="unit_id_{{ $loop->index }}" class="form-control unit_id"
                                                            name="unit_id[{{ $loop->index }}]"
                                                            data-product-id="{{ $item->product_id }}">
                                                            @foreach ($units as $unit)
                                                                <option @selected($unit->id == $item->unit_id) value="{{ $unit->id }}">{{ $unit->name }}</option>
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
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary" id="save-invoice"> تحويل</button>
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
    </div>
@endsection

@section('js')
    <!-- Internal Notify js -->
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>


    <script>
        $(document).ready(function() {
            @if (session()->has('error_created'))
                notif({
                    msg: "لا يمكنك حفظ التحويل اذا كان المخزن المحول منه هو نفس المخزن المحول اليه",
                    type: "error"
                });
            @endif

            @if (session()->has('success_created'))
                notif({
                    msg: "تم حفظ البيانات بنجاح",
                    type: "success"
                });
            @endif

            @if (session()->has('success_updated'))
                notif({
                    msg: "تم تحديث البيانات بنجاح",
                    type: "success"
                });
            @endif

            @if (session()->has('success_deleted'))
                notif({
                    msg: "تم حذف البيانات بنجاح",
                    type: "success"
                });
            @endif

            $('select').select2();
            let addedProducts = [];


            document.getElementById('add-product').focus();

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
                                    stock_id: $('.stock_id_from').val(),
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
                                            <td>${rowCount}</td>
                                            <td>
                                                <input type="text" id="product_name_${unitIndex}" name="product_name[${unitIndex}]" class="form-control product_name" value="${data.product.name}" readonly>
                                                <input type="hidden" id="product_id_${unitIndex}" name="product_id[${unitIndex}]" class="product_id" value="${data.product.id}">
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
                                            stock_id: $('.stock_id_from').val(),
                                        })
                                    }).done(function(data) {
                                        if (data.success) {
                                            tbody.append(row);
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
                                } else if (showError) {
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
                let productId = currentRow.find('.product_id').val();
                addedProducts = addedProducts.filter(id => id !== productId);
                $('.btn-add-invoice').each(function(index,
                    button) {
                    const $btn = $(
                        button); // تحويل العنصر إلى jQuery object

                    if ($btn.data('id') == currentRow.find('.product_id').val()) {
                        $btn.addClass(
                            'btn-primary'
                        ).removeClass(
                            'btn-white disabled');
                    }
                });
                currentRow.remove();
            });

            $('#product-rows').on('change', '.unit_id', function() {
                const unitSelect = $(this);
                let unitId = unitSelect.val();
                let productId = unitSelect.data('product-id');
                const currentRow = unitSelect.closest('tr');
                let qtyInput = currentRow.find('.qty');
                let stockId = $('.stock_id_from');
                checkQuantity(unitSelect, productId, qtyInput, stockId);
            });

            $(document).on('change', '.stock_id_from', function() {
                $.ajax({
                    url: '{{ route('select2.stock') }}',
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    data: {
                        stockId: $(this).val(),
                    },
                }).done(function(data) {
                    if (data.success) {
                        $('#product-rows').empty();
                        $('.stock_id_to').empty();
                        $('#body_search').empty();
                        let option =
                            ` ${Array.isArray(data.stocks) ? data.stocks.map(stock => `<option value="${stock.id}">${stock.name}</option>`).join('') : ''}`;
                        $('.stock_id_to').append(option);
                        let rows = '';
                        data.products.forEach((product, index) => {
                            if (product.pivot.quantity == 0) return;

                            rows += `
                                <a href="#" class="product-pick-item product-pick-item--grad btn btn-primary btn-add-invoice" data-id="${product.id}">${product.name} <span class="product-pick-qty">${product.qty}</span></a>
                            `;
                        });

                        $('#body_search').append(rows);

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
            });


            $('#product-rows').on('blur', '.qty', function() {
                const currentRow = $(this).closest('tr');
                let qty = parseFloat($(this).val()) || 0;
                let unitSelect = currentRow.find('.unit_id'); // تحديد الـ select
                let productId = currentRow.find('.product_id'); // قيمة المنتج
                let qtyInput = currentRow.find('.qty');
                let stockId = $('.stock_id_from');
                checkQuantity(unitSelect, productId.val(), qtyInput, stockId);
            });

            $('#transformForm').validate({
                rules: {
                    stock_id_from: {
                        required: true
                    },
                    stock_id_to: {
                        required: true,
                    },
                },
                messages: {
                    stock_id_from: {
                        required: "يجب اختيار المخزن المرحل منه"
                    },
                    stock_id_to: {
                        required: "يجب اختيار المخزن المرحل اليه",
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


            $(document).on('click', '.btn-add-invoice', function(e) {
                e.preventDefault();

                const button = $(this);
                const productId = button.data('id');

                // ✅ تحقق من القائمة المؤقتة بدل الـ DOM
                if (addedProducts.includes(productId)) {
                    notif({
                        msg: '⚠️ هذا المنتج موجود بالفعل في الفاتورة',
                        type: 'error'
                    });
                    return;
                }

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
                            tbody.append(row);
                            $('.unit_id').select2();
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

                            $(`#row_sub_total_${unitIndex}`).rules('add', {
                                required: true,
                                number: true,
                                messages: {
                                    required: "يرجى إدخال المجموع الفرعي",
                                    number: "المجموع الفرعي يجب أن يكون رقمًا"
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

            let searchXhr;
            let timer;
            $('#search').on('keyup', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
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
                            stock_id: $('#stock_id_from').val(),
                            type: 3
                        },
                        success: function(data) {
                            let tbody = $('#body_search');
                            tbody.empty();
                            let rows = '';
                            data.products.forEach((product, index) => {
                                if (product.quantity == 0) return;

                                rows += `
                                    <a href="#" class="product-pick-item product-pick-item--grad btn btn-primary btn-add-invoice" data-id="${product.id}">${product.name} <span class="product-pick-qty">${product.qty}</span></a>
                                `;
                            });

                            tbody.append(rows);
                        }
                    });
                }, 300); // تأخير 300 ملي ثانية
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
            }).done(function(data) {
                if (!data.success) {
                    notif({
                        msg: data.message,
                        type: 'error',
                    });
                    resetSelect2(unitSelect, qtyInput);
                }
            }).fail(function() {
                notif({
                    msg: '⚠️ حدث خطأ أثناء الاتصال بالسيرفر.',
                    type: 'error',
                });
            });
        }

        function resetSelect2(unitSelect, qtyInput) {
            let firstOption = unitSelect.find('option:first').val();
            console.log(firstOption);
            unitSelect.val(firstOption).trigger('change.select2'); // إعادة تعيين الـ select
            qtyInput.val(1); // إعادة تعيين الكمية إلى 1
        }
    </script>
@endsection
