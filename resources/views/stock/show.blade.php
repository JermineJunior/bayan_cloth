@extends('layouts.app')
@section('css')
    <!--- Internal Select2 css-->
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-label {
            font-weight: 500;
            font-size: 16px;
            margin-left: 4px;
        }

        @media print {

            #filter-form,
            #action-buttons,
            .breadcrumb-header {
                display: none !important;
            }
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> ادارة المخزن</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    رصيد المخزن</span>
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
                    <div class="d-flex justify-content-between gap-2">
                        <h5 class="content-title mb-0 my-auto">رصيد المخزن: {{ $stock->name }}</h5>

                        <a href="{{ route('stock.index') }}" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('stock.show', $stock->id) }}" id="filter-form"
                        class="form-horizontal form-label-left">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="product_id">المنتج</label>
                                <select name="product_id" id="product_id" class="form-control">
                                    <option value="">الكل</option>
                                    @foreach ($allProducts as $product)
                                        <option value="{{ $product->id }}"
                                            {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="search">بحث</label>
                                <input type="text" name="search" id="search" class="form-control"
                                    placeholder="بحث بالاسم أو الباركود" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4 mb-3 d-flex align-items-center">
                                <div class="form-check mt-4 d-flex align-items-center">
                                    <input type="checkbox" name="hide_zero" id="hide_zero" value="1"
                                        class="form-check-input position-static"
                                        style="margin: 0 0 0 8px; width: 18px; height: 18px;"
                                        {{ request('hide_zero') ? 'checked' : '' }}>
                                    <label class="form-check-label mb-0" for="hide_zero">إخفاء المنتجات برصيد صفر</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mb-3">
                            <button type="submit" class="btn btn-primary ml-2">بحث</button>
                            <a href="{{ route('stock.show', $stock->id) }}" class="btn btn-secondary ml-2">مسح</a>
                        </div>
                    </form>

                    <div id="action-buttons" class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-info" onclick="window.print()">
                            <i class="fa fa-print"></i>&nbsp;&nbsp;طباعة
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="wd-5p border-bottom-0">#</th>
                                    <th class="wd-20p border-bottom-0">الباركود</th>
                                    <th class="wd-30p border-bottom-0">المنتج</th>
                                    <th class="wd-20p border-bottom-0">الفئة</th>
                                    <th class="wd-25p border-bottom-0">الكمية الحالية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr class="{{ $product->pivot->quantity <= 0 ? 'text-muted' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->barcode }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name ?? '' }}</td>
                                        <td
                                            class="{{ $product->pivot->quantity <= 0 ? 'text-danger font-weight-bold' : '' }}">
                                            {{ $product->qty_text }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">لا توجد منتجات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <p class="tx-12 tx-gray-500 mb-0">عدد المنتجات المعروضة: {{ $products->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection
@section('js')
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#product_id').select2();
        });
    </script>
@endsection
