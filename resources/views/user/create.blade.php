@extends('layouts.app')
@section('css')
    <!-- Internal Nice-select css  -->
    <link href="{{ URL::asset('assets/plugins/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المستخدمين</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ اضافة
                    مستخدم</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            @if ($errors->any())
                <div class="alert alert-outline-warning" role="alert">
                    <button aria-label="Close" danger="close" data-dismiss="alert" type="button">
                        <span aria-hidden="true">&times;</span></button>
                    @foreach ($errors->all() as $error)
                        <strong>خطأ!</strong> {{ $error }}
                    @endforeach
                </div>
            @endif
            <div class="card">
                <div class="card-header pb-0">
                    <div class="col-sm-1 col-md-12">
                        <div class="float-left">
                            <a href="javascript:history.back()" class="btn btn-warning">
                                <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;رجوع
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="userForm" action="{{ route('user.store') }}" method="post">
                        @csrf
                        <div class="">
                            <div class="row mg-b-20">
                                <div class="parsley-input col-md-6" id="fnWrapper">
                                    <span class="tx-danger">*</span><label>الاسم : </label>
                                    <input class="form-control form-control" data-parsley-class-handler="#lnWrapper"
                                        name="name" type="text" value="{{ old('name') }}">
                                </div>

                                <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0">
                                    <span class="tx-danger">*</span><label> اسم المستخدم: </label>
                                    <input class="form-control" data-parsley-class-handler="#lnWrapper" name="username"
                                        type="username" value="{{ old('username') }}">
                                </div>
                            </div>

                        </div>

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0">
                                <span class="tx-danger">*</span><label>كلمة المرور: </label>
                                <input class="form-control" data-parsley-class-handler="#lnWrapper" name="password"
                                    id="password" placeholder type="password">
                            </div>

                            <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0">
                                <span class="tx-danger">*</span><label> تاكيد كلمة المرور: </label>
                                {{-- <input class="form-control" data-parsley-class-handler="#lnWrapper" name="confirm-password"
                                     type="password"> --}}
                                <input class="form-control" name="password_confirmation"type="password">
                            </div>
                        </div>
                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0">
                                <div class="form-group">
                                    <span class="tx-danger">*</span><label> صلاحية المستخدم</label>
                                    <select name="roles_name" id="roles_name" class="form-control nice-select">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0">
                                <span class="tx-danger">*</span><label> الفروع: </label>
                                <select name="branch[]" id="branch" class="form-control branch" multiple>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <button class="btn btn-main-primary pd-x-20" type="submit">اضافة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!-- Internal Nice-select js-->
    <script src="{{ URL::asset('assets/plugins/jquery-nice-select/js/jquery.nice-select.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/jquery-nice-select/js/nice-select.js') }}"></script>

    <!--Internal  Parsley.min js -->
    <script src="{{ URL::asset('assets/plugins/parsleyjs/parsley.min.js') }}"></script>
    {{-- Select2 --}}
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    {{-- form_validation --}}
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>


    <script>
        $(document).ready(function() {
            $('.branch').select2({
                placeholder: 'من فضلك قم باختيار احد الفروع على الاقل',
                dir: 'rtl'
            });

            $('#userForm').validate({
                rules: {
                    name: {
                        required: true,
                    },
                    username: {
                        required: true,
                        uniqueUserName: true

                    },
                    password: {
                        required: true,
                    },
                    'password_confirmation': {
                        required: true,
                        equalTo: "#password"
                    },
                    roles_name: {
                        required: true,
                        number: true,
                    },
                    'branch[]': {
                        required: true
                    }

                },
                messages: {
                    name: {
                        required: "يرجى إدخال الاسم .",
                    },
                    username: {
                        required: "يجب ادخال اسم المستخدم.",
                        uniqueUserName: "هذا اسم المستخدم موجود بالفعل في النظام."
                    },
                    password: {
                        required: "يرجى إدخال كلمة المرور.",
                    },
                    'password_confirmation': {
                        required: "يرجى إدخال تاكيد كلمة المرور.",
                        equalTo: "يرجى تأكيد كلمة المرور بشكل صحيح.",
                    },
                    roles_name: {
                        required: "يرجى إدخال اسم الصلاحية.",
                        number: "يجب أن تكون الصلاحية عدداً.",
                    },
                    'branch[]': {
                        required: "يجب اختيار احد الفروع على الاقل"
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

            $.validator.addMethod("uniqueUserName", function(value, element) {
                let isValid = false;

                if (value == '') {
                    return isValid = true;
                }
                $.ajax({
                    url: '{{ route('user.checkUserName') }}', // مسار التحقق من الباركود
                    data: {
                        username: value,
                    },
                    type: 'GET',
                    async: false, // تأكد من أن التحقق يتم بشكل متزامن
                    success: function(response) {
                        isValid = response.isUnique; // إذا كانت النتيجة صحيحة (فريد)
                    }
                });
                return isValid;
            }, "هذا اسم المستخدم موجود بالفعل في النظام.");
        });
    </script>
@endsection
