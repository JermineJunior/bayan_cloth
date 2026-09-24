@extends('layouts.login')

@section('css')
    <link href="{{ URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fa;
        }

        .branch-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin: 50px 0;
        }

        .branch-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .branch-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .branch-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .select-button {
            display: inline-block;
            padding: 8px 22px;
            background-color: #0162e8;
            color: #fff;
            border-radius: 25px;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .select-button:hover {
            background-color: #014cc1;
        }

        .system-image-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .system-image-container img {
            max-width: 90%;
            height: auto;
        }

        .section-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-top: 40px;
            color: #0162e8;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row no-gutter">

            <!-- الفروع -->
            <div class="col-md-8 col-lg-6 col-xl-5 d-flex align-items-center">
                <div class="container p-4">
                    <h2 class="section-title">يرجى اختيار الفرع</h2>
                    <div class="branch-grid">
                        @foreach ($branches as $branch)
                            <div class="branch-card">
                                <div class="branch-name">{{ $branch->name }}</div>
                                <a class="select-button" href="{{ route('branch.choose', $branch->id) }}">تحديد</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- صورة النظام -->
            <div class="col-md-4 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
                <div class="system-image-container w-100">
                    <img src="{{ asset('image/login/bayan.jpg') }}" alt="شعار النظام">
                </div>
            </div>

        </div>
    </div>
@endsection
