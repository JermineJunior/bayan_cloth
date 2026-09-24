<!-- resources/views/report/low_stock_products.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="text-center mb-5 animate__animated animate__fadeInDown">
            <h3 class="fw-bold text-warning display-5">⚠️ تقرير المنتجات التي تحتاج لإعادة الطلب</h3>
            <p class="text-muted fs-5">المنتجات التي تجاوزت حد الطلب</p>
        </div>

        <!-- بطاقة التقرير -->
        <div class="card shadow-lg border-0 rounded-4 animate__animated animate__fadeInUp">
            <div class="card-body p-4">
                @if ($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-center align-middle">
                            <thead class="bg-warning text-dark">
                                <tr>
                                    <th class="py-3">اسم المنتج</th>
                                    <th class="py-3">المخزن الحالي</th>
                                    <th class="py-3">الحد الأدنى للمخزن</th>
                                    <th class="py-3">النقص في المخزون</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="fw-bold text-secondary">{{ $product->name }}</td>
                                        <td class="text-danger fw-bold">{{ $product->convertQuantity($product->stocks->sum('pivot.quantity'))}}</td>
                                        <td class="text-primary fw-bold">{{ $product->convertQuantity($product->reorder_level) }}</td>
                                        <td>
                                            <span class="badge bg-danger fs-6 px-3 py-2 text-white">
                                                {{ $product->convertQuantity($product->reorder_level - $product->stocks->sum('pivot.quantity')) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <h4 class="text-muted">✅ لا توجد منتجات تحتاج لإعادة الطلب</h4>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
    </div>

    <!-- تضمين مكتبة Animate.css لتحسين التأثيرات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- تحسينات CSS إضافية -->
    <style>
        .table th,
        .table td {
            vertical-align: middle;
            font-size: 1.1rem;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .card {
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .badge {
            font-size: 1rem;
            padding: 0.6rem 1.2rem;
        }
    </style>
@endsection
