@extends('layouts.app')

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto">تأكيد الاستلام</h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة المستندات</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    @if ($errors->any())
                        <div class="alert alert-outline-danger" role="alert">
                            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                <span aria-hidden="true">&times;</span></button>
                            @foreach ($errors->all() as $error)
                                <strong>خطأ!</strong> {{ $error }}
                            @endforeach
                        </div>
                    @endif

                    @if (session()->has('success_created'))
                        <div class="alert alert-success" role="alert">{{ session('success_created') }}</div>
                    @endif

                    @if (session()->has('success_deleted'))
                        <div class="alert alert-success" role="alert">{{ session('success_deleted') }}</div>
                    @endif

                    <form action="{{ route('receipts.index') }}" method="GET" class="row">
                        <div class="col-md-4">
                            <label for="status">الحالة</label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending" @selected($status === 'pending')>بانتظار التأكيد</option>
                                <option value="confirmed" @selected($status === 'confirmed')>مؤكد</option>
                                <option value="all" @selected($status === 'all')>الكل</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="type">النوع</label>
                            <select name="type" id="type" class="form-control">
                                <option value="all" @selected($type === 'all')>الكل</option>
                                <option value="purchase" @selected($type === 'purchase')>مشتريات</option>
                                <option value="transform" @selected($type === 'transform')>تحويل</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">تصفية</button>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped text-md-nowrap">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th>الرقم</th>
                                    <th>التاريخ</th>
                                    <th>المورد / من - إلى</th>
                                    <th>أنشأه</th>
                                    <th>الحالة</th>
                                    <th>عرض</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documents as $document)
                                    <tr>
                                        <td>{{ $document['type_label'] }}</td>
                                        <td>{{ $document['number'] }}</td>
                                        <td>{{ $document['date'] }}</td>
                                        <td>{{ $document['source'] }}</td>
                                        <td>{{ $document['created_by'] }}</td>
                                        <td>
                                            @if ($document['confirmation'])
                                                <span class="badge badge-success">مؤكد</span>
                                                <small class="d-block">
                                                    {{ $document['confirmation']->user?->name ?? '-' }}
                                                    -
                                                    {{ $document['confirmation']->created_at?->format('Y-m-d H:i') }}
                                                </small>
                                            @else
                                                <span class="badge badge-warning">بانتظار التأكيد</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-outline-info btn-sm"
                                                href="{{ route('receipts.show', [$document['type'], $document['id']]) }}">
                                                عرض
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا توجد مستندات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
