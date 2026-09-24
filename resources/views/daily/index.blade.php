@extends('layouts.app')

@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />

    <!-- تنسيقات المودال المحسنة -->
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
            /* إضافة شريط تمرير إذا لزم الأمر */
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            /* تقليل الهامش العلوي */
            padding: 20px;
            border-radius: 10px;
            width: 70%;
            /* زيادة عرض المودال */
            max-width: 800px;
            /* تحديد أقصى عرض */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 24px;
            color: #007bff;
        }

        .modal-header .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-header .close:hover {
            color: #000;
        }

        .modal-body {
            max-height: 60vh;
            /* تحديد أقصى ارتفاع */
            overflow-y: auto;
            /* إضافة شريط تمرير إذا لزم الأمر */
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header {
            font-size: 18px;
            font-weight: bold;
            padding: 10px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            text-align: center;
        }

        .cell {
            padding: 10px;
            background: #e9ecef;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }

        .notes {
            grid-column: span 2;
            background: #d4edda;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn-close {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-close:hover {
            background: #c82333;
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> إدارة النظام المحاسبي </h5><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ قيود اليومية</span><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ إدارة قيود اليومية</span>
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
                                    msg: " تم إضافة القيد بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif

                    @if (session()->has('success_deleted'))
                        <script>
                            window.onload = function() {
                                notif({
                                    msg: " تم حذف القيد بنجاح",
                                    type: "success"
                                })
                            }
                        </script>
                    @endif

                    <div class="float-left">
                        <a href="javascript:history.back()" class="btn btn-warning">
                            <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                        </a>
                    </div>
                </div>

                {{--  card table   --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="example1">
                            <thead>
                                <tr>
                                    <th>نوع القيد</th>
                                    <th>أنشأ بواسطة</th>
                                    <th>رقم القيد</th>
                                    <th>تاريخ القيد</th>
                                    <th> البيان</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operations as $operation)
                                    <tr>
                                        <td>{{ $operation->type == 5 ? 'قيد يومية ' : 'قيد مركب' }}</td>
                                        <td>{{ $operation->user }}</td>
                                        <td>{{ $operation->constant_number }}</td>
                                        <td>{{ $operation->date }}</td>
                                        <td>{{ $operation->node }}</td>
                                        <td>
                                            <a href="{{ route('compound.edit', $operation->id) }}"
                                                class="btn btn-outline-success btn-sm"><i
                                                    class="fa fa-edit"></i>&nbsp;&nbsp;تعديل</a>
                                            <a class="modal-effect btn btn-outline-danger btn-sm" data-target="#deleteModal"
                                                data-toggle="modal" href="#" data-id="{{ $operation->id }}"
                                                data-type="{{ $operation->type }}" title="حذف"> <i
                                                    class="fa fa-trash"></i>&nbsp;&nbsp;حذف</a>
                                            <a class="modal-effect btn btn-outline-info btn-sm" data-target="#showModal"
                                                data-toggle="modal" href="#"
                                                data-madin="{{ $operation->madin_name }}"
                                                data-dain="{{ $operation->dain_name }}"
                                                data-price="{{ number_format($operation->price, 2) }}"
                                                title="عرض التفاصيل">
                                                <i class="fa fa-eye"></i>&nbsp;&nbsp;عرض التفاصيل</a>
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
                    <form id="deleteForm" action="Alldaily/destroy" method="post">
                        {{ method_field('delete') }}
                        {{ csrf_field() }}
                        <input type="hidden" id="id" name="id" class="form-control">
                        <h4 class="tx-danger mg-b-20">هل تريد حذف هذا السجل بالفعل ؟</h4>
                        <p class="mg-b-20 mg-x-20">في حالة الموافقة لا يمكنك التراجع عن هذا الاجراء !</p>
                        <button class="btn ripple btn-danger pd-x-25" type="submit">نعم , حذف السجل</button>
                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">إلغاء</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for showing entry details --}}
    <div class="modal" id="showModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>عرض قيد يومي</h2>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="header">حساب مدين</div>
                    <div class="header">حساب دائن</div>
                    <div class="header">المبلغ</div>

                    <div id="maidn" class="cell">الحساب A</div>
                    <div id="dain" class="cell">الحساب B</div>
                    <div id="price" class="cell">1000</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">إلغاء</button>
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
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <script>
        $('#showModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            let maidn = button.data('madin');
            let dain = button.data('dain');
            let price = button.data('price');
            let modal = $(this);
            modal.find('#maidn').text(maidn);
            modal.find('#dain').text(dain);
            modal.find('#price').text(price);
        });

        $('#deleteModal').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget);
            let id = button.data('id');
            let type = button.data('type');
            let modal = $(this);
            if (type == 5) {
                $('#deleteForm').attr('action', '{{ route('daily.destroy', ':type') }}'.replace(':id', id));
            } else if (type == 6) {
                $('#deleteForm').attr('action', '{{ route('compound.destroy', ':id') }}'.replace(':id', id));
            }
            modal.find('.modal-body #id').val(id);
        })
    </script>
@endsection
