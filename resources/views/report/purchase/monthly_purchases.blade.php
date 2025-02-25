<!-- resources/views/reports/daily_purchases.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">تقرير المشتريات الشهرية</h1>

        @if (isset($message))
            <div class="alert alert-warning text-center">
                <strong>{{ $message }}</strong>
            </div>
        @else
            <div class="row mb-4">
                <!-- بطاقة إجمالي المشتريات -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-dollar-sign fa-3x text-primary"></i>
                            <h3 class="mt-3">إجمالي المشتريات</h3>
                            <h4 class="text-primary">{{ number_format($totalPurchases, 2) }} ج.س</h4>
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

                <!-- بطاقة إجمالي الموردين -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x text-warning"></i>
                            <h3 class="mt-3">عدد الموردين</h3>
                            <h4 class="text-warning">
                                {{ $purchases->whereNotNull('supplier_id')->unique('supplier_id')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول المشتريات -->
            <div class="card">
                <div class="card-header">
                    <h5>تفاصيل المشتريات الشهرية</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>عدد المنتجات</th>
                                <th>المورد</th>
                                <th>المبلغ الإجمالي</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->invoice_number }}</td>
                                    <td>{{ $purchase->details()->count() }}</td>
                                    <td>{{ $purchase->supplier->tree4_name ?? 'لا يوجد مورد' }}</td>
                                    <td>{{ number_format($purchase->total_amount, 2) }}</td>
                                    <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
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
