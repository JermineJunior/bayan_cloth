@extends('layouts.app')

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> التقارير </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير مردودات المبيعات خلال فترة معينة  </span>
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
                    <div class="d-flex">
                        <h5 class="content-title mb-0 my-auto"> تقرير مردودات المبيعات خلال فترة معينة  :</h5>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="card-body">
                            <form method="POST" action="{{ route('reports.sale_return.show') }}" id="demo-form2" class="form-horizontal form-label-left">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="inputName" class="control-label"> تاريخ
                                            البداية
                                        </label>
                                        <input type="date" name="start" value="<?php echo date('Y-m-d'); ?>"
                                            class="form-control" required>
                                    </div>
                                    </br>
                                    <div class="col">
                                        <span class="tx-danger">*</span><label for="inputName" class="control-label"> تاريخ
                                            النهاية
                                        </label>
                                        <input type="date" name="end" value="<?php echo date('Y-m-d'); ?>"
                                            class="form-control" required>
                                    </div>
                                </div>
                                </br>

                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary"> بـحـث</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--/div-->
        <!-- row closed -->
    </div>
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
