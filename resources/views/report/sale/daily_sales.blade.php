<!-- resources/views/reports/daily_sales.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">تقرير المبيعات اليومية</h1>

        @if (isset($message))
            <div class="alert alert-warning text-center">
                <strong>{{ $message }}</strong>
            </div>
        @else
            <div class="row mb-4">
                <!-- بطاقة إجمالي المبيعات -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-dollar-sign fa-3x text-primary"></i>
                            <h3 class="mt-3">إجمالي المبيعات</h3>
                            <h4 class="text-primary">{{ number_format($totalSales, 2) }} ج.س</h4>
                        </div>
                    </div>
                </div>

                <!-- بطاقة عدد الفواتير -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-file-invoice fa-3x text-success"></i>
                            <h3 class="mt-3">عدد الفواتير</h3>
                            <h4 class="text-success">{{ $totalInvoices }}</h4>
                        </div>
                    </div>
                </div>

                <!-- بطاقة إجمالي العملاء -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x text-warning"></i>
                            <h3 class="mt-3">عدد العملاء</h3>
                            <h4 class="text-warning">
                                {{ $sales->whereNotNull('customer_id')->unique('customer_id')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول المبيعات -->
            <div class="card">
                <div class="card-header">
                    <h5>تفاصيل المبيعات اليومية</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>عدد المنتجات</th>
                                <th>العميل</th>
                                <th>المبلغ الإجمالي</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td>{{ $sale->invoice_number }}</td>
                                    <td>{{ $sale->details()->count() }}</td>
                                    <td>{{ $sale->customer->tree4_name ?? 'لا يوجد عميل' }}</td>
                                    <td>{{ number_format($sale->total_amount, 2) }}</td>
                                    <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    </div>
    </div>
@endsection
