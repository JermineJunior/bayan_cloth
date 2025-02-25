@extends('layouts.app')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto"> أنشطة الحسابات </h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    المستوي الثالث</span>
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
                    <div class="col-sm-6 col-md-4 col-xl-3 mg-t-20">
                        @can('اضافة نشاط')
                            <a class="modal-effect btn btn-primary btn-block" data-effect="effect-super-scaled"
                                data-toggle="modal" href="#addModal"><i class="fa fa-plus"> </i>&nbsp;&nbsp;إضافة حساب
                                </i></a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="example1">
                            <thead>
                                <tr>
                                    <th class="wd-5p border-bottom-0">متسلسل</th>
                                    <th class="wd-5p border-bottom-0">كود الحساب </th>
                                    <th class="wd-5p border-bottom-0">الحساب</th>
                                    <th class="wd-5p border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($tree3s as $tree)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $tree->tree3_code }}</td>
                                        <td>{{ $tree->tree3_name }}</td>
                                        <td>
                                            @can('تعديل نشاط')
                                                <a class="modal-effect btn btn-outline-success btn-sm"
                                                    data-effect="effect-super-scaled" data-toggle="modal" href="#modaldemo8"
                                                    data-id="{{ $tree->id }}" data-tree3_name="{{ $tree->tree3_name }}"
                                                    data-tree3_code="{{ $tree->tree3_code }}"
                                                    data-tree2_name="{{ $tree->tree2->tree2_name }}" title="تعديل">
                                                    <i class="fa fa-edit">
                                                    </i>&nbsp;&nbsp;تعديل </i></a>
                                            @endcan
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
        {{-- //edit model section   --}}
        <!-- Basic modal -->
        <div class="modal" id="addModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">اضافة إسم الحساب </h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('tree3.store') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="inputName" class="control-label"> الحساب الرئيسي</label>
                                <select name="tree2_id" id="tree2_id" class="form-control" style="width: 100%">
                                    @foreach ($tree2s as $tree2)
                                        <option value="{{ $tree2->id }}">{{ $tree2->tree2_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <span class="tx-danger">*</span><label>اسم الحساب</label>
                                <input id="tree3_name" required type="text" name="tree3_name" class="form-control">
                            </div>
                            <div class="modal-footer">
                                <button class="btn ripple btn-primary" type="submit">اضافة</button>
                                <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">إغلاق</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal" id="modaldemo8">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">تعديل إسم الحساب </h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form id="edit_tree3" action="tree3/update" method="post">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="tree2_name" class="control-label"> الحساب الرئيسي</label>
                                <input type="hidden" id="id_ed" name="id" class="form-control">
                                <input disabled class="form-control" id="tree2_name_ed">
                            </div>
                            <div class="form-group">
                                <span class="tx-danger">*</span><label>اسم الحساب</label>
                                <input id="tree3_name_ed" required type="text" name="tree3_name" class="form-control">
                            </div>
                            <div class="modal-footer">
                                <button class="btn ripple btn-primary" type="submit">تحديث البيانات </button>
                                <button class="btn ripple btn-secondary" data-dismiss="modal"
                                    type="button">إغلاق</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Basic modal -->
        <!-- row closed -->
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
        $('#modaldemo8').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var tree2_name = button.data('tree2_name')
            var tree3_code = button.data('tree3_code')
            var tree3_name = button.data('tree3_name')

            var modal = $(this)
            $('#edit_tree3').attr('action', '{{ route('tree3.update', ':id') }}'.replace(':id', id));
            modal.find('.modal-body #id_ed').val(id);
            modal.find('.modal-body #tree2_name_ed').val(tree2_name);
            modal.find('.modal-body #tree3_code_ed').val(tree3_code);
            modal.find('.modal-body #tree3_name_ed').val(tree3_name);

        });

        $('select').select2();
    </script>
@endsection
