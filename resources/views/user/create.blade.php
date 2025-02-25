@extends('layouts.app')
@section('css')
    <!-- Internal Nice-select css  -->
    <link href="{{ URL::asset('assets/plugins/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet" />
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
                <div class="card-body">
                    <form id="userForm" action="{{ route('user.store') }}" method="post">
                        @csrf
                        <div class="">
                            <div class="row mg-b-20">
                                <div class="parsley-input col-md-6" id="fnWrapper">
                                    <span class="tx-danger">*</span><label>الاسم : </label>
                                    <input class="form-control form-control" data-parsley-class-handler="#lnWrapper"
                                        name="name"  type="text" value="{{ old('name') }}">
                                </div>

                                <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0">
                                    <label>البريد الالكتروني: </label>
                                    <input class="form-control" data-parsley-class-handler="#lnWrapper" name="email"
                                        type="email" value="{{ old('email') }}">
                                </div>
                            </div>

                        </div>

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0">
                                <span class="tx-danger">*</span><label>كلمة المرور: </label>
                                <input class="form-control" data-parsley-class-handler="#lnWrapper" name="password"
                                    id="password" placeholder  type="password">
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
                                <span class="tx-danger">*</span><label> رقم التلفون: </label>
                                <input class="form-control" data-parsley-class-handler="#lnWrapper" name="phone_number"
                                     type="number" value="{{ old('phone_number') }}">
                            </div>
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
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/js/form-validation.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>

    <script>
        $('#userForm').validate({
            rules: {
                name: {
                    required: true,
                },
                email: {
                    uniqueEmail: true

                },
                phone_number: {
                    required: true,
                    uniquePhoneNumber: true
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
                }

            },
            messages: {
                name: {
                    required: "يرجى إدخال اسم المستخدم.",
                },
                emil: {
                    uniqueEmail: "هذا البريد الالكتروني مستخدم من قبل."
                },
                phone_number: {
                    required: "يرجى إدخال رقم الهاتف.",
                    uniquePhoneNumber: "هذا الرقم مستخدم من قبل."
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

        $.validator.addMethod("uniqueEmail", function(value, element) {
            let isValid = false;
            $.ajax({
                url: '{{ route('user.checkEmail') }}', // مسار التحقق من الباركود
                data: {
                    email: value,
                },
                type: 'GET',
                async: false, // تأكد من أن التحقق يتم بشكل متزامن
                success: function(response) {
                    isValid = response.isUnique; // إذا كانت النتيجة صحيحة (فريد)
                }
            });
            return isValid;
        }, "هذا البريد الالكتروني موجود بالفعل في النظام.");

        $.validator.addMethod("uniquePhoneNumber", function(value, element) {
            let isValid = false;
            $.ajax({
                url: '{{ route('user.checkPhoneNumber') }}', // مسار التحقق من الباركود
                data: {
                    phone_number: value,
                },
                type: 'GET',
                async: false, // تأكد من أن التحقق يتم بشكل متزامن
                success: function(response) {
                    isValid = response.isUnique; // إذا كانت النتيجة صحيحة (فريد)
                }
            });
            return isValid;
        }, "هذا رقم الهاتف موجود بالفعل في النظام.");
    </script>
@endsection
