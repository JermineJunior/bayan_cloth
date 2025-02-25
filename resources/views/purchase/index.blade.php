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
                <h5 class="content-title mb-0 my-auto"> ادارة فواتير المشتريات</h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    قائمة فاتورة المشتريات</span>
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
                        <div class="alert alert-outline-warning" role="alert">
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

                    <div class="col-sm-6 col-md-4 col-xl-3 mg-t-20">
                        @can('اضافة فاتورة مشتريات')
                            <a class="btn btn-primary btn-block" href="{{ route('purchase_invoice.create') }}"><i
                                    class="fa fa-plus"> </i>&nbsp;&nbsp;إضافة فاتورة مشتريات
                                </i></a>
                        @endcan
                    </div>
                    {{-- <p class="tx-12 tx-gray-500 mb-2">Example of Valex Simple Table. <a href="">Learn more</a></p>  --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="example1">
                            <thead>
                                <tr>
                                    <th class="wd-3p border-bottom-0">#</th>
                                    <th class="wd-15p border-bottom-0">رقم الفاتورة</th>
                                    <th class="wd-15p border-bottom-0"> اجمالي المبلغ</th>
                                    <th class="wd-15p border-bottom-0"> الخصم</th>
                                    <th class="wd-15p border-bottom-0">قيمة الضريبة</th>
                                    <th class="wd-15p border-bottom-0">تاريخ الفاتورة</th>
                                    <th class="wd-10p">الحالة</th>
                                    <th class="wd-15p border-bottom-0">العمليات </th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($purchase_invoices as $purchase_invoice)
                                    <tr data-id="{{ $purchase_invoice->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $purchase_invoice->invoice_number }}</td>
                                        <td>{{ $purchase_invoice->total_amount }}</td>
                                        <td>{{ $purchase_invoice->discount_amount }}</td>
                                        <td>{{ $purchase_invoice->tax_amount }}</td>
                                        <td>{{ $purchase_invoice->invoice_date }}</td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-{{ $purchase_invoice->status ? 'success' : 'danger' }} toggle-status-btn"
                                                data-id="{{ $purchase_invoice->id }}">
                                                {{ $purchase_invoice->status == 1 ? 'نشط' : 'غير نشط' }}
                                            </button>
                                        </td>
                                        <td>
                                            @can('تعديل فاتورة مشتريات')
                                                <a class="btn btn-outline-success btn-sm"
                                                    href="{{ route('purchase_invoice.edit', $purchase_invoice->id) }}"
                                                    title="تعديل">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('حذف فاتورة مشتريات')
                                                <a class="modal-effect btn btn-outline-danger btn-sm" data-target="#deleteModal"
                                                    data-toggle="modal" href="#deleteModal"
                                                    data-id="{{ $purchase_invoice->id }}" title="حذف">
                                                    <i class="fa fa-trash">
                                                    </i></a>
                                            @endcan
                                            @if (!$purchase_invoice->returnInvoice)
                                                @can('اضافة مردودات المشتريات')
                                                    <a class="btn btn-outline-info btn-sm"
                                                        href="{{ route('purchase_invoice_return.create', ['purchase_invoice' => $purchase_invoice->id]) }}"
                                                        title="اضافة مردودات مشتريات">
                                                        <i class="fa fa-reply"></i>
                                                    </a>
                                                @endcan
                                            @endif
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
                    <form action="purchase_invoices/destroy" method="post" id="delete_purchase_invoice">
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
                $('#delete_purchase_invoice').attr('action',
                    '{{ route('purchase_invoice.destroy', ':id') }}'.replace(
                        ':id', id));
            });

            // Handling status toggle button click
            $('.toggle-status-btn').click(function() {
                var purchase_invoiceId = $(this).data('id');
                var button = $(this);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('purchase_invoice.toggleStatus', '') }}/' + purchase_invoiceId,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        button.toggleClass('btn-success btn-danger');
                        button.text(response.status == 1 ? 'نشط' : 'غير نشط');
                        notif({
                            msg: 'تم تغيير حالة الحملة بنجاح',
                            type: 'success'
                        });
                    },
                    error: function() {
                        notif({
                            msg: 'حدث خطأ أثناء تغيير الحالة',
                            type: 'error'
                        });
                    }
                });
            });
        });
    </script>
@endsection
