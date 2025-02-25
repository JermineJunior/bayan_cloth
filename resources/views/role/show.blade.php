@extends('layouts.app')

@section('css')
    <!--Internal Font Awesome -->
    <link href="{{ URL::asset('assets/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <style>
        .permission-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .permission-label {
            display: flex;
            align-items: center;
            font-size: 16px;
            gap: 12px;
            padding: 10px;
            background: #ffffff;
            border-radius: 6px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .permission-label:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
        }

        .permission-icon {
            background: #007bff;
            color: white;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 18px;
        }

        .custom-input {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 10px;
        }

        .page-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الصلاحيات</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ عرض الأذونات</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 page-title">تفاصيل الصلاحية</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold"> اسم الصلاحية</label>
                        <input type="text" class="form-control custom-input" name="name" readonly value="{{ $role->name }}">
                    </div>

                    <div class="mt-4">
                        <h5 class="mb-3">الأذونات المرتبطة</h5>
                        <div class="permission-container">
                            @foreach ($role->permissions as $permission)
                                <label class="permission-label">
                                    <span class="permission-icon"><i class="fas fa-shield-alt"></i></span>
                                    {{ $permission->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
