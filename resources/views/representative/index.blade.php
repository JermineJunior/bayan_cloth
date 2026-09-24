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
                <h5 class="content-title mb-0 my-auto"> ادارة المندوبين</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    قائمة المندوبين</span>
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
                            @can('اضافة مندوب')
                                <a class="modal-effect btn btn-primary" data-effect="effect-super-scaled" data-toggle="modal"
                                    href="#addModal"><i class="fa fa-plus"> </i>&nbsp;&nbsp;إضافة مندوب
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
                                    <th class="wd-5p border-bottom-0">#</th>
                                    <th class="wd-20p border-bottom-0">الاسم</th>
                                    <th class="wd-20p border-bottom-0">رقم الهاتف</th>
                                    <th class="wd-15p border-bottom-0">العمليات </th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($representatives as $representative)
                                    <tr data-id="{{ $representative->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $representative->tree4_name }}</td>
                                        <td>{{ $representative->phone }}</td>
                                        <td>
                                            @can('تعديل المندوب')
                                                <a class="modal-effect btn btn-outline-success btn-sm"
                                                    data-effect="effect-super-scaled" data-toggle="modal" href="#editModal"
                                                    data-id="{{ $representative->id }}" data-name="{{ $representative->tree4_name }}"
                                                    data-phone="{{ $representative->phone }}" data-email="{{ $representative->email }}"
                                                    data-address="{{ $representative->address }}" data- title="تعديل">
                                                    <i class="fa fa-edit">
                                                    </i></a>
                                            @endcan
                                            @can('حذف المندوب')
                                                <a class="modal-effect btn btn-outline-danger btn-sm" data-target="#deleteModal"
                                                    data-toggle="modal" href="#deleteModal" data-id="{{ $representative->id }}"
                                                    title="حذف">
                                                    <i class="fa fa-trash">
                                                    </i></a>
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

        <!-- Basic modal -->
        <div class="modal" id="addModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">إضافة مندوب جديد </h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form id="addRepresentative" action="{{ route('representative.store') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <span class="tx-danger">*</span><label for="exampleInputEmail1">إسم المندوب </label>
                                <input type="text" name="name" id="name" class="form-control" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">البريد الالكتروني للمندوب
                                </label>
                                <input type="email" name="email" id="email" autocomplete="off" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">رقم هاتف المندوب </label>
                                <input type="number" name="phone" id="phone" class="form-control"
                                    autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">عنوان المندوب </label>
                                <input type="text" name="address" id="address" class="form-control"
                                    autocomplete="off">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button class="btn ripple btn-primary" type="submit">إضافة </button>
                            <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">إغلاق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Basic modal -->

        <div class="modal" id="editModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">تعديل مندوب </h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="#" method="post" id="edit_representative">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="hidden" id="representative_id" value="">
                                <span class="tx-danger">*</span><label>اسم المندوب</label>
                                <input id="name_ed" type="text" name="name" class="form-control"
                                    autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label>البريد الالكتروني للمندوب</label>
                                <input id="email_ed" type="email" name="email" class="form-control"
                                    autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label>رقم هاتف المندوب</label>
                                <input id="phone_ed" type="number" name="phone" class="form-control"
                                    autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label>عنوان المندوب</label>
                                <input id="address_ed" type="text" name="address" class="form-control"
                                    autocomplete="off">
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
                    <form action="representatives/destroy" method="post" id="delete_representative">
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
    {{-- form_validation --}}
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>

    {{-- delete script  --}}
    <script>
        $(document).ready(function() {
            // Handling data passing to edit and delete modals
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var email = button.data('email');
                var phone = button.data('phone');
                var address = button.data('address');
                var modal = $(this);
                $('#edit_representative').attr('action', '{{ route('representative.update', ':id') }}'.replace(':id',
                    id));
                modal.find('.modal-body #name_ed').val(name);
                modal.find('.modal-body #email_ed').val(email);
                modal.find('.modal-body #phone_ed').val(phone);
                modal.find('.modal-body #address_ed').val(address);
                modal.find('.modal-body #representative_id').val(id);
            });

            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var modal = $(this);
                $('#delete_representative').attr('action', '{{ route('representative.destroy', ':id') }}'.replace(
                    ':id', id));
            });

            $('#addRepresentative').validate({
                rules: {
                    name: {
                        required: true,
                        uniqueNameRepresentative: true,
                    },
                    email: {
                        uniqueEmailrepresentative: true,
                        email: true,
                    },
                    phone: {
                        uniquephonerepresentative: true,
                        number: true
                    }
                },
                messages: {
                    name: {
                        required: "يرجى إدخال اسم المندوب.",
                    },
                    email: {
                        email: "الرجاء إدخال بريد إلكتروني صالح.",
                    },
                    phone: {
                        required: "يرجى إدخال رقم المندوب.",
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.closest('.input-group').length) {
                        error.insertAfter(element.closest('.input-group'));
                    } else if (element.hasClass('select2-hidden-accessible')) {
                        // ✅ التعامل مع `select2`
                        error.insertAfter(element.next('.select2'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                errorElement: "span",
                errorClass: "text-danger",
                highlight: function(element) {
                    $(element).closest('.form-group').addClass('has-error');
                },
                unhighlight: function(element) {
                    $(element).closest('.form-group').removeClass('has-error');
                }
            });

            $('#edit_representative').validate({
                rules: {
                    name: {
                        required: true,
                        uniqueNameRepresentative: true,
                    },
                    email: {
                        uniqueEmailrepresentative: true,
                        email: true,
                    },
                    phone: {
                        uniquephonerepresentative: true,
                        number: true
                    }
                },
                messages: {
                    name: {
                        required: "يرجى إدخال اسم المندوب.",
                    },
                    email: {
                        email: "الرجاء إدخال بريد إلكتروني صالح.",
                    },
                    phone: {
                        required: "يرجى إدخال رقم المندوب.",
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.closest('.input-group').length) {
                        error.insertAfter(element.closest('.input-group'));
                    } else if (element.hasClass('select2-hidden-accessible')) {
                        // ✅ التعامل مع `select2`
                        error.insertAfter(element.next('.select2'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                errorElement: "span",
                errorClass: "text-danger",
                highlight: function(element) {
                    $(element).closest('.form-group').addClass('has-error');
                },
                unhighlight: function(element) {
                    $(element).closest('.form-group').removeClass('has-error');
                }
            });

            $('.toggle-status-btn').click(function() {
                var representativeId = $(this).data('id');
                var button = $(this);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('representative.toggleStatus', '') }}/' + representativeId,
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

            $.validator.addMethod("uniqueEmailrepresentative", function(value, element) {
                let isValid = false;
                if(value == ''){
                    return isValid = true;
                }
                $.ajax({
                    url: '{{ route('representative.checkEmail') }}',
                    data: {
                        email: value,
                        id: $('#representative_id').val()

                    },
                    type: 'GET',
                    async: false, // تأكد من أن التحقق يتم بشكل متزامن
                    success: function(response) {
                        isValid = response.isUnique;
                    }
                });
                return isValid;
            }, "هذا البريد الالكتروني موجود بالفعل في النظام.");

            $.validator.addMethod("uniquephonerepresentative", function(value, element) {
                let isValid = false;
                if(value == ''){
                    return isValid = true;
                }
                $.ajax({
                    url: '{{ route('representative.checkPhone') }}',
                    data: {
                        phone: value,
                        id: $('#representative_id').val()
                    },
                    type: 'GET',
                    async: false, // تأكد من أن التحقق يتم بشكل متزامن
                    success: function(response) {
                        isValid = response.isUnique;
                    }
                });
                return isValid;
            }, "هذا رقم المندوب موجود بالفعل في النظام.");

            $.validator.addMethod("uniqueNameRepresentative", function(value, element) {
                let isValid = false;
                $.ajax({
                    url: '{{ route('representative.checkName') }}',
                    data: {
                        name: value,
                        id: $('#representative_id').val()
                    },
                    type: 'GET',
                    async: false, // تأكد من أن التحقق يتم بشكل متزامن
                    success: function(response) {
                        isValid = response.isUnique;
                    }
                });
                return isValid;
            }, "هذا اسم المندوب موجود بالفعل في النظام.");



        });
    </script>
@endsection
