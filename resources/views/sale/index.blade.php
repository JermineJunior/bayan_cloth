@extends('layouts.app')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> ادارة فواتير المبيعات</h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    قائمة فاتورة المبيعات</span>
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
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم حفط البيانات بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif

                    @if (session()->has('success_updated'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم تحديث البيانات بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif
                    @if (session()->has('success_deleted'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم حذف البيانات بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif

                    <div class="col-sm-6 col-md-4 col-xl-12 mg-t-20">
                        <div class="d-flex justify-content-between gap-2">
                            @can('اضافة فاتورة مبيعات')
                                <a class="btn btn-primary" href="{{ route('sale_invoice.create') }}"><i class="fa fa-plus">
                                    </i>&nbsp;&nbsp;إضافة فاتورة مبيعات
                                    </i></a>
                            @endcan

                            <a href="javascript:history.back()" class="btn btn-warning">
                                <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                            </a>
                        </div>
                    </div>
                    {{-- <p class="tx-12 tx-gray-500 mb-2">Example of Valex Simple Table. <a href="">Learn more</a></p>  --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="example1">
                            <thead>
                                <tr>
                                    <th class="wd-10p border-bottom-0">#</th>
                                    <th class="wd-10p border-bottom-0">رقم الفاتورة</th>
                                    <th class="wd-20p border-bottom-0"> تاريخ الفاتورة</th>
                                    <th class="wd-20p border-bottom-0"> اسم العميل</th>
                                    <th class="wd-20p border-bottom-0">صافي الفاتورة</th>
                                    <th class="wd-20p border-bottom-0">العمليات </th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($sale_invoices as $sale_invoice)
                                    <tr data-id="{{ $sale_invoice->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sale_invoice->invoice_number }}</td>
                                        <td>{{ $sale_invoice->date }}</td>
                                        <td>{{ $sale_invoice->customer->tree4_name ?? '-' }}</td>
                                        <td>{{ number_format($sale_invoice->total_amount, 2) }}</td>
                                        <td>
                                            @if ($sale_invoice->deferend_amount > 0)
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="{{ route('sale_invoice.pay.create', $sale_invoice->id) }}"
                                                    title="سداد">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </a>
                                            @endif
                                            @can('طباعة فاتورة مبيعات')
                                                <a class="btn btn-outline-info btn-sm"
                                                    href="{{ route('sale_invoice.pdf', $sale_invoice->id) }}" title="طباعة">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            @endcan
                                            @if ($sale_invoice->allocations_count > 0)
                                                <span class="badge badge-success">مسدد فواتير</span>
                                            @endif
                                            @can('تعديل فاتورة مبيعات')
                                                @if ($sale_invoice->allocations_count == 0)
                                                    <a class="btn btn-outline-success btn-sm"
                                                        href="{{ route('sale_invoice.edit', $sale_invoice->id) }}"
                                                        title="تعديل">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif
                                            @endcan
                                            @can('حذف فاتورة مبيعات')
                                                @if ($sale_invoice->allocations_count == 0)
                                                    <a class="modal-effect btn btn-outline-danger btn-sm"
                                                        data-target="#deleteModal" data-toggle="modal" href="#deleteModal"
                                                        data-id="{{ $sale_invoice->id }}" title="حذف">
                                                        <i class="fa fa-trash">
                                                        </i></a>
                                                @endif
                                            @endcan
                                            {{-- @if (!$sale_invoice->returnInvoice)
                                                @can('اضافة مردودات المبيعات')
                                                    <a class="btn btn-outline-info btn-sm"
                                                        href="{{ route('sale_invoice_return.create', ['sale_invoice' => $sale_invoice->id]) }}"
                                                        title="اضافة مردودات مبيعات">
                                                        <i class="fa fa-reply"></i>
                                                    </a>
                                                @endcan
                                            @endif --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->

    </div>
    </div>
    <!-- row closed -->
    </div>
    {{-- // modal delete alert    --}}
    <div class="modal" id="deleteModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content tx-size-sm">
                <div class="modal-body tx-center pd-y-20 pd-x-20">
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                            aria-hidden="true">&times;</span></button> <i
                        class="icon icon ion-ios-close-circle-outline tx-100 tx-danger lh-1 mg-t-20 d-inline-block"></i>
                    <form action="sale_invoices/destroy" method="post" id="delete_sale_invoice">
                        @csrf
                        @method('DELETE')
                        <h4 class="tx-danger mg-b-20">هل تريد حذف هذا السجل بالفعل ؟</h4>
                        <input type="hidden" id="name" readonly name="name" class="form-control">
                        <p class="mg-b-20 mg-x-20">في حالة الموافقة لا يمكنك التراجع عن هذا الاجراء !</p>
                        <button class="btn ripple btn-danger pd-x-25" type="submit">نعم , حذف السجل</button>
                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">إلغاء</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    {{-- modals  --}}
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>

    {{-- delete script  --}}
    <script>
        $(document).ready(function() {
            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var modal = $(this);
                $('#delete_sale_invoice').attr('action', '{{ route('sale_invoice.destroy', ':id') }}'
                    .replace(
                        ':id', id));
            });

        });
    </script>
@endsection
