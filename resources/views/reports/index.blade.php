@extends('layouts.app')

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto">التقارير</h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ التقارير</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php
        $categories = collect(config('reports_menu'))->map(function ($cat) {
            $cat['items'] = collect($cat['items'])
                ->filter(fn($i) => \Illuminate\Support\Facades\Route::has($i['route']) && auth()->user()->can($i['can']))
                ->values();
            return $cat;
        })->filter(fn($cat) => $cat['items']->isNotEmpty());
    @endphp

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5 class="content-title mb-0 my-auto">التقارير :</h5>
                </div>
                <div class="card-body">
                    @if ($categories->isEmpty())
                        <div class="alert alert-info mb-0">لا توجد تقارير متاحة لهذا المستخدم.</div>
                    @else
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach ($categories as $key => $cat)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab"
                                        href="#tab-{{ $key }}" role="tab">{{ $cat['title'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content pt-3">
                            @foreach ($categories as $key => $cat)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="tab-{{ $key }}" role="tabpanel">
                                    <div class="row">
                                        @foreach ($cat['items'] as $item)
                                            <div class="col-md-4 col-sm-6">
                                                <a href="{{ route($item['route']) }}"
                                                    class="btn btn-outline-primary btn-block text-right mb-3">{{ $item['label'] }}</a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
