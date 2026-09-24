@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">تقرير المبيعات {{ $sales->first()->customer->tree4_name ?? '' }}</h1>

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
                            <h3 class="mt-3">اجمالي المبيعات</h3>
                            <h4 class="text-primary">{{ number_format($sales->sum('total_amount'), 2) }} ج.س</h4>
                        </div>
                    </div>
                </div>

                <!-- بطاقة عدد الفواتير -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-check fa-3x text-success"></i>
                            <h3 class="mt-3">تاريخ البداية</h3>
                            <h4 class="text-success">{{ $start }}</h4>
                        </div>
                    </div>
                </div>

                <!-- بطاقة إجمالي العملاء -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-times fa-3x text-info"></i>
                            <h3 class="mt-3">تاريخ النهاية</h3>
                            <h4 class="text-info">
                                {{ $end }}</h4>
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
                    @if ($sales->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>عدد المنتجات</th>
                                    <th>المبلغ الإجمالي</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->details()->count() }}</td>
                                        <td>{{ number_format($sale->total_amount, 2) }}</td>
                                        <td>{{ $sale->date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                    <div class="text-center">
                        <h4>لا يوجد بيانات متوفرة لهذا التاريخ.</h4>
                    </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
    </div>
    </div>
@endsection