@extends('layouts.app')
@section('css')
    <!-- Internal Nice-select css  -->
    <link href="{{ URL::asset('assets/plugins/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet" />
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الملف الشخصي</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تعديل
                    تعديل الملف الشخصي</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>خطا</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session()->has('success'))
                <script>
                    window.onload = function() {
                        notif({
                            msg: " تم تحديث البيانات بنجاح",
                            type: "success"
                        })
                    }
                </script>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-right">
                            {{-- <a class="btn btn-primary btn-sm" href="{{ route('profile.index') }}">رجوع</a> --}}
                        </div>
                    </div><br>
                    <form action="{{ route('user.profile.update') }}" method="POST" id="userForm">
                        @csrf
                        <div class="">

                            <div class="row mg-b-20">
                                <div class="parsley-input col-md-6" id="fnWrapper">
                                    <label>اسم المستخدم: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}"
                                        required>
                                </div>

                                <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                                    <label>البريد الالكتروني: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="email"value="{{ $user->email }}"
                                        required>
                                </div>
                            </div>

                        </div>

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label>كلمة المرور: <span class="tx-danger">*</span></label>
                                <input type="password" name="password" class="form-control" id="password">
                            </div>

                            <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label> تاكيد كلمة المرور: <span class="tx-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control"> 
                            </div>
                        </div>



                        <div class="mg-t-30">
                            <button class="btn btn-main-primary pd-x-20" type="submit"> تحديث البيانات</button>
                        </div>
                    </form>
                </div>
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
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <script>
        $('#userForm').validate({
            rules: {
                name: {
                    required: true,
                },
                email: {
                    uniqueEmail: true

                },
                password_confirmation: {
                    required: function() {
                        return $('#password').val().trim() !== ''; 
                    },
                    equalTo: "#password"
                }

            },
            messages: {
                name: {
                    required: "يرجى إدخال اسم المستخدم.",
                },
                emil: {
                    uniqueEmail: "هذا البريد الالكتروني مستخدم من قبل."
                },
                'password_confirmation': {
                    required: "يرجى ادخال تاكيد كلمة المرور.",
                    equalTo: "يرجى تأكيد كلمة المرور بشكل صحيح."
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

        $.validator.addMethod("uniqueEmail", function(value, element) {
            let isValid = false;
            $.ajax({
                url: '{{ route('user.checkEmail') }}', // مسار التحقق من الباركود
                data: {
                    email: value,
                    id: {{ $user->id }}
                },
                type: 'GET',
                async: false, // تأكد من أن التحقق يتم بشكل متزامن
                success: function(response) {
                    isValid = response.isUnique; // إذا كانت النتيجة صحيحة (فريد)
                }
            });
            return isValid;
        }, "هذا البريد الالكتروني موجود بالفعل في النظام.");

    </script>
@endsection
