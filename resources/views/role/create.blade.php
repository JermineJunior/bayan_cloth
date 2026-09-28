@extends('layouts.app')
@section('css')
    <!--Internal  Font Awesome -->
    <link href="{{ URL::asset('assets/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <!--Internal  treeview -->
    <style>
        .permission-container {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        .permission-group {
            margin-bottom: 20px;
            margin-top: 12px;
        }

        .permission-group:last-child {
            margin-bottom: 0;
        }

        .permission-items {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .permission-label {
            display: flex;
            align-items: center;
            font-size: 16px;
            gap: 10px;
            padding: 8px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .permission-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #dee2e6;
        }

        .permission-group-title {
            font-size: 15px;
            font-weight: bold;
            color: #333;
            margin-bottom: 0;
        }

        .permission-group-toggle {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            font-size: 13px;
            font-weight: normal;
            color: #6c757d;
            white-space: nowrap;
            cursor: pointer;
        }

        .custom-checkbox {
            transform: scale(1.2);
        }

        #toggle-permissions {
            transition: all 0.3s ease-in-out;
        }

        #toggle-permissions:hover {
            background-color: #007bff;
            color: white;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الصلاحيات</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إضافة نوع مستخدم</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>خطأ!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="roleForm" action="{{ route('role.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card mg-b-20">
                    <div class="card-body">
                        <div class="main-content-label mg-b-5">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label><span class="tx-danger">*</span> اسم الصلاحية</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">الأذونات</h5>
                            <button type="button" id="toggle-permissions" class="btn btn-sm btn-primary">
                                تحديد الكل
                            </button>
                        </div>

                        <div id="permission-error" class="text-danger"></div>

                        <div class="permission-container">
                            @foreach (groupPermissions($permissions) as $group)
                                <div class="permission-group">
                                    <div class="permission-group-header">
                                        <h6 class="permission-group-title">{{ $group['name'] }}</h6>
                                        <label class="permission-group-toggle">
                                            <input type="checkbox" class="custom-checkbox permission-group-checkbox">
                                            تحديد الكل
                                        </label>
                                    </div>
                                    <div class="permission-items">
                                        @foreach ($group['permissions'] as $permission)
                                            <label class="permission-label">
                                                <input type="checkbox" class="custom-checkbox permission-checkbox"
                                                    name="permission[]" value="{{ $permission->name }}">
                                                {{ $permission->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-12 text-center mt-3">
                            <button type="submit" class="btn btn-main-primary">تأكيد</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('js')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Internal Treeview js -->
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>
    <script>
        function syncPermissionGroups() {
            $(".permission-group").each(function() {
                let items = $(this).find(".permission-checkbox");
                let groupCheckbox = $(this).find(".permission-group-checkbox");
                let checkedCount = items.filter(":checked").length;
                let allChecked = items.length > 0 && checkedCount === items.length;

                groupCheckbox.prop("checked", allChecked);
                groupCheckbox.prop("indeterminate", checkedCount > 0 && !allChecked);
            });
        }

        $(document).on("change", ".permission-group-checkbox", function() {
            $(this).closest(".permission-group").find(".permission-checkbox").prop("checked", this.checked);
        });

        $(document).on("change", ".permission-checkbox", function() {
            syncPermissionGroups();
        });

        $(document).ready(function() {
            syncPermissionGroups();

            $("#toggle-permissions").click(function() {
                let checkboxes = $(".permission-checkbox");
                let allChecked = checkboxes.length === checkboxes.filter(":checked").length;
                if (allChecked) {
                    checkboxes.prop("checked", false);
                    $(this).text("تحديد الكل").removeClass("btn-danger").addClass("btn-primary");
                } else {
                    checkboxes.prop("checked", true);
                    $(this).text("إلغاء التحديد").removeClass("btn-primary").addClass("btn-danger");
                }
                syncPermissionGroups();
            });

            $('#roleForm').validate({
                rules: {
                    name: {
                        required: true
                    },
                    'permission[]': {
                        required: function() {
                            return $('input[name="permission[]"]:checked').length === 0;
                        }
                    }
                },
                messages: {
                    name: {
                        required: "ادخل اسم الصلاحية من فضلك"
                    },
                    'permission[]': {
                        required: "ادخل صلاحية واحدة على الاقل."
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "permission[]") {
                        error.appendTo("#permission-error");
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
        });
    </script>
@endsection
