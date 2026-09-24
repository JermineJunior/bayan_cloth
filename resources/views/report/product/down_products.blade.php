<!-- resources/views/report/daily_sales.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold text-primary display-4 animate__animated animate__fadeInDown">📊 تقرير المنتجات الاقل مبيعًا</h1>
        <p class="text-muted fs-5">نظرة شاملة على أداء المنتجات في النظام</p>
    </div>

    <!-- بطاقة التقرير -->
    <div class="card shadow-lg border-0 rounded-3 animate__animated animate__fadeInUp">
        <div class="card-header bg-primary text-white text-center fw-bold fs-4">
            📈 ملخص المبيعات
        </div>
        <div class="card-body">
            <table class="table table-hover table-striped text-center align-middle ">
                <thead>
                    <tr>
                        <th class="py-3">#</th>
                        <th class="py-3">اسم المنتج</th>
                        <th class="py-3">الكمية المباعة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topProducts as $index => $item)
                        <tr class="animate__animated animate__fadeInUp animate__delay-{{ $index }}s">
                            <td class="fw-bold fs-5">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-semibold text-primary fs-5">{{ $item->product->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success fs-5 px-3 py-2 shadow-sm text-white">{{ $item->total_qty }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>

<!-- تضمين مكتبة Animate.css لتحسين التأثيرات -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<!-- تحسينات CSS إضافية -->
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .card {
        overflow: hidden;
    }
    .badge {
        font-size: 1.2rem;
    }
</style>
@endsection
