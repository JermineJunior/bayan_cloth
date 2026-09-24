@extends('layouts.app')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
    {{-- select2 --}}
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <style>
        .tabs {
            background: white;
            width: 100%;
            max-width: 1000;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .tab-header {
            display: flex;
            flex-wrap: wrap;
            background: #f0f0f0;
            border-bottom: 1px solid #ddd;
        }

        .tab-header div {
            flex: 1 0 auto;
            /* مرونة */
            padding: 15px 20px;
            text-align: center;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s, color 0.3s;
            min-width: 120px;
            /* كل تبويب له عرض مبدأي */
        }

        .tab-header div:hover {
            background: #e0e0e0;
        }

        .tab-header .active {
            background: white;
            border-bottom: 3px solid #3498db;
            color: #3498db;
        }

        .tab-content {
            padding: 20px;
            display: none;
            animation: fadeIn 0.5s;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> جرد الاصناف</h5>
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
                {{-- <div class="card-body"> --}}
                <div class="tabs">
                    <div class="tab-header" id="tabHeader">
                        @foreach ($stocks as $stock)
                            <div class="{{ $loop->index == 0 ? 'active' : '' }}" onclick="showTab({{ $loop->index }})">
                                {{ $stock->name }}</div>
                        @endforeach
                    </div>

                    <form action="{{ route('burren.store') }}" method="POST" id="burrenForm">
                        @csrf
                        @foreach ($stocks as $stock)
                            <div class="tab-content {{ $loop->index == 0 ? 'active' : '' }}">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td>اسم المنتج</td>
                                                <td>الرصيد الافتتاحي </td>
                                                <td>الوارد</td>
                                                <td>الصادر </td>
                                                <td>المتوفر </td>
                                                <td>الوحدة </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $product)
                                                <tr>
                                                    <td>
                                                        {{ $product->name }}
                                                        <input type="hidden" name="product_id[]"
                                                            value="{{ $product->id }}">
                                                        <input type="hidden" name="stock_id[]"
                                                            value="{{ $stock->id }}">
                                                    </td>

                                                    <td>

                                                        @php
                                                            $qty = $product
                                                                ->stockDetails()
                                                                ->where([
                                                                    'product_id' => $product->id,
                                                                    'type' => 8,
                                                                    'stock_id' => $stock->id,
                                                                    'bracn_id' => session('branch_id'),
                                                                ])
                                                                ->first();
                                                        @endphp
                                                        <input type="number" name="openQty[]" class="form-control mb-2"
                                                            value="{{ $qty->wared ?? 0 }}" readonly>
                                                    </td>
                                                    @php
                                                        $wared = $product
                                                            ->stockDetails()
                                                            ->where('stock_id', $stock->id)
                                                            ->where('type', '!=', 8)
                                                            ->where('branch_id', session('branch_id'))
                                                            ->sum('wared');
                                                    @endphp
                                                    <td>
                                                        <input type="number" name="wared[]" class="form-control mb-2"
                                                            value="{{ $wared }}" readonly>
                                                    </td>

                                                    @php
                                                        $sader = $product
                                                            ->stockDetails()
                                                            ->where('stock_id', $stock->id)
                                                            ->where('type', '!=', 8)
                                                            ->where('branch_id', session('branch_id'))
                                                            ->sum('sader');
                                                    @endphp
                                                    <td>
                                                        <input type="number" name="sader[]" class="form-control mb-2"
                                                            value="{{ $sader }}" readonly>
                                                    </td>
                                                    @php
                                                        $sader = $product
                                                            ->stockDetails()
                                                            ->where('stock_id', $stock->id)
                                                            ->where('branch_id', session('branch_id'))
                                                            ->sum('sader');
                                                        $wared = $product
                                                            ->stockDetails()
                                                            ->where('stock_id', $stock->id)
                                                            ->where('branch_id', session('branch_id'))
                                                            ->sum('wared');
                                                        $result = abs($wared - $sader);
                                                    @endphp
                                                    <td>
                                                        <input type="number" name="balance[]" class="form-control mb-2"
                                                            value="{{ $result }}">
                                                    </td>
                                                    {{-- <td>
                                                        <input type="text" class="form-control" value="{{ $product->units->first()->name }}" readonly>
                                                    </td> --}}
                                                    <td>
                                                        <select name="unit_id[]" class="form-control">
                                                            @foreach ($product->units as $unit)
                                                                <option value="{{ $unit->id }}">{{ $unit->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach

                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary mb-3">حفظ</button>
                        </div>
                    </form>
                </div>
                {{-- </div> --}}
            </div>
        </div>
    </div>
@endsection
@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    {{-- select2 --}}
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    {{-- form validation --}}
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    {{-- modals  --}}
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <script>
        function showTab(index) {
            const headers = document.querySelectorAll('.tab-header div');
            const contents = document.querySelectorAll('.tab-content');

            headers.forEach(header => header.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            headers[index].classList.add('active');
            contents[index].classList.add('active');
        }

        $('select').select2();

        @if ($errors->any())
            <
            div class = "alert alert-outline-warning"
            role = "alert" >
                <
                button aria - label = "Close"
            class = "close"
            data - dismiss = "alert"
            type = "button" >
                <
                span aria - hidden = "true" > & times; < /span></button >
            @foreach ($errors->all() as $error)
                <
                strong > خطأ! < /strong> {{ $error }}
            @endforeach < /div>
        @endif
        @if (session()->has('success_created'))
            window.onload = function() {
                notif({
                    msg: " تم حفط البيانات بنجاح",
                    type: "success"
                })
            }
        @endif

        @if (session()->has('success_updated'))
            window.onload = function() {
                notif({
                    msg: " تم تحديث البيانات بنجاح",
                    type: "success"
                })
            }
        @endif
        @if (session()->has('success_deleted'))
            window.onload = function() {
                notif({
                    msg: " تم حذف البيانات بنجاح",
                    type: "success"
                })
            }
        @endif

        $('#burrenForm').validate({
            rules: {
                product_id: {
                    required: true,
                },
                stock_id: {
                    required: true,
                },
                quantity_old: {
                    required: true,
                    number: true,
                    min: 0
                },
                quantity_new: {
                    required: true,
                    number: true,
                    min: 0
                }
            },
            messages: {
                product_id: {
                    required: "يرجى إدخال رقم المنتج.",
                },
                stock_id: {
                    required: "يرجى إدخال رقم المخزن.",
                },
                quantity_old: {
                    required: "يرجى إدخال الكمية بالمخزن.",
                    number: "يجب أن تكون الكمية بالمخزن عدداً.",
                    min: "يجب ان تكون الكميةبالمخزن اكبر من الصفراً."
                },
                quantity_new: {
                    required: "يرجى إدخال الكمية الحالية.",
                    number: "يجب أن تكون الكمية الحالية عدداً.",
                    min: "يجب أن تكون الكمية الحالية اكبر من الصفر."
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
    </script>
@endsection
