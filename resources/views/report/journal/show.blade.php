@extends('layouts.app')
@section('css')
<style>
    @media print {
        #print_Button {
            display: none !important;
        }
    }
</style>
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">التقارير المحاسبية</h4><span
                class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير دفتر اليومية
            </span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')
<!-- row -->
<div class="row row-sm">
    <div class="col-md-12 col-xl-12">
        <div class=" main-content-body-invoice" id="print">
            <div class="card card-invoice">
                <div class="card-body">
                    <div class="invoice-header">
                        <h1 class="invoice-title">تقرير دفتر اليومية <span></span>
                            <div class="col-md">
                                <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p>
                                {{-- <p><span> &nbsp;</span>&nbsp; <span>&nbsp;</span></p> --}}
                                <p class="invoice-info-row" style="font-size: 14px"><span>تاريخ البداية :</span>
                                    {{$start}}<span></span></p>
                                <p class="invoice-info-row" style="font-size: 14px"><span>تاريخ النهاية :</span>
                                    {{$end}}<span></span></p>
                            </div>
                        </h1>
                        <div class="billed-from">
                            <div class="col"> <img class="img-bill" style="width: 240px; height: 240px;"
                                    src="{{ asset('image/logo/'.GeneralSiteSettings('logo'))  }}" /> </div>
                            <br />
                            {{-- <h5> {{GeneralSiteSettings('location')}} </h5< /br> --}}
                                <h5>{{GeneralSiteSettings('name')}}</h5>
                                <p>
                                هاتف : {{GeneralSiteSettings('phone')}}</br>
                                البريد الإلكتروني:{{GeneralSiteSettings('email')}}</p>
                        </div><!-- billed-from -->
                    </div><!-- invoice-header -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped mg-b-0 text-md-nowrap">
                                        <thead>
                                            <tr>
                                                {{-- <th>متسلسل</th> --}}
                                                <th> مدين</th>
                                                <th> دائن</th>
                                                <th> المبلغ</th>
                                                <th>أنشأ بواسطة</th>
                                                <th> البيان</th>
                                                <th>رقم القيد</th>
                                                <th>تاريخ القيد</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($operations as $operation)
                                            <tr>
                                                <td> {{$operation->tree4_madin->tree4_name}}</td>
                                                <td> {{$operation->tree4_dain->tree4_name}}</td>
                                                <td> {{number_format($operation->price ,2)}}</td>
                                                <td>{{$operation->user->name}}</td>
                                                <td>{{$operation->node}}</td>
                                                <td>{{$operation->constant_number}}</td>
                                                <td>{{$operation->created_at->format('Y/m/d')}}</td>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div><!-- bd -->

                            </div><!-- bd -->
                        </div><!-- bd -->
                    </div>
                    </br>
                    {{-- <div class="row">
                        <div class="col">
                            <div class="alert alert-primary" role="alert" style="text-align: center">
                                <h4>مرحبـــاً بـك</h4>
                                <span></span>
                            </div>
                        </div>
                    </div> --}}
                    {{-- //footer the page invoices --}}
                    <div class="row">
                        <div class="col">
                            <h6 style="text-align: center">
                                <span> {{GeneralSiteSettings('location')}}
                                    - البريد الإلكتروني : {{GeneralSiteSettings('email')}}
                                    - هاتف : {{GeneralSiteSettings('phone')}}
                                </span>

                                </br>
                            </h6>
                        </div>
                    </div>
                    {{-- //footer the page invoices --}}
                    <hr class="mg-b-40">
                    <button class="btn btn-primary float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                        <i class="mdi mdi-printer ml-1"></i> طبـاعة التقرير</button>

                </div>
            </div>
        </div>
    </div><!-- COL-END -->
</div>
<!-- row closed -->
</div>
<!-- Container closed -->
</div>
<!-- main-content closed -->
@endsection
@section('js')
<!--Internal  Chart.bundle js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<script>
    function printDiv() {
        var printContents = document.getElementById('print').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }

</script>
@endsection