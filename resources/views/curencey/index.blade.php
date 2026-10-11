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
                <h5 class="content-title mb-0 my-auto"> ادارة العملات</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    قائمة العملات</span>
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

                    <div class="col-sm-6 col-md-8 col-xl-12 mg-t-20">
                        <div class="d-flex justify-content-between gap-2">
                            @can('اضافة العملة')
                                <a class="modal-effect btn btn-primary" data-effect="effect-super-scaled" data-toggle="modal"
                                    href="#addModal">
                                    <i class="fa fa-plus"></i>&nbsp;&nbsp;إضافة عملة
                                </a>
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
                                    <th class="wd-10p">القيمة</th>
                                    <th class="wd-10p">الرمز</th>
                                    <th class="wd-15p border-bottom-0">العمليات </th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($curenceies as $curencey)
                                    <tr data-id="{{ $curencey->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $curencey->name }}</td>
                                        <td>{{ number_format($curencey->amount) }} </td>
                                        <td>{{ $curencey->symbol }}</td>
                                        <td>
                                            @can('تعديل العملة')
                                                <a class="modal-effect btn btn-outline-success btn-sm"
                                                    data-effect="effect-super-scaled" data-toggle="modal" href="#editModal"
                                                    data-id="{{ $curencey->id }}" data-name="{{ $curencey->name }}"
                                                    data-amount="{{ number_format($curencey->amount) }}"
                                                    data-symbol="{{ $curencey->symbol }}" title="تعديل">
                                                    <i class="fa fa-edit">
                                                    </i></a>
                                            @endcan
                                            @if ($curencey->id !== 1)
                                                @can('حذف العملة')
                                                    <a class="modal-effect btn btn-outline-danger btn-sm"
                                                        data-target="#deleteModal" data-toggle="modal" href="#deleteModal"
                                                        data-id="{{ $curencey->id }}" title="حذف">
                                                        <i class="fa fa-trash">
                                                        </i></a>
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

        <!-- Basic modal -->
        <div class="modal" id="addModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">إضافة عملة جديدة </h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form id="curenceyForm" action="{{ route('curencey.store') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <span class="tx-danger">*</span><label for="exampleInputEmail1">إسم العملة </label>
                                <input type="text" name="name" id="name" class="form-control" autocomplete="off"
                                    required>
                            </div>
                            <div class="form-group">
                                <span class="tx-danger">*</span><label for="exampleInputEmail1">قيمة العملة </label>
                                <input type="text" name="amount" id="amount" class="form-control amount"
                                    autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <span class="tx-danger">*</span><label for="exampleInputEmail1">رمز العملة </label>
                                <input type="text" name="symbol" id="symbol" class="form-control"
                                    autocomplete="off" placeholder="مثلا جنيه سوداني او دولار امريكي او ريال سعودي"
                                    required>
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
                        <h6 class="modal-title">تعديل عملة </h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="#" method="post" id="edit_curencey">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" value="" name="id" id="curencey_id">
                            <div class="form-group">
                                <span class="tx-danger">*</span><label>اسم العملة</label>
                                <input id="name_ed" type="text" name="name" class="form-control"
                                    autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <span class="tx-danger">*</span><label>قيمة العملة</label>
                                <input id="amount_ed" type="text" name="amount" class="form-control amount"
                                    autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <span class="tx-danger">*</span><label>قيمة العملة</label>
                                <input id="symbol_ed" type="text" name="symbol" class="form-control"
                                    autocomplete="off" required
                                    placeholder="مثلا حنيه سوداني او دولار امريكي او ريال سعودي">
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
                    <form action="curenceys/destroy" method="post" id="delete_curencey">
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
                var amount = button.data('amount');
                var symbol = button.data('symbol');
                var modal = $(this);
                $('#edit_curencey').attr('action', '{{ route('curencey.update', ':id') }}'.replace(':id',
                    id));
                modal.find('.modal-body #name_ed').val(name);
                modal.find('.modal-body #amount_ed').val(amount);
                modal.find('.modal-body #symbol_ed').val(symbol);
                modal.find('.modal-body #curencey_id').val(id);
            });

            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var modal = $(this);
                $('#delete_curencey').attr('action', '{{ route('curencey.destroy', ':id') }}'.replace(
                    ':id', id));
            });

            $('#curenceyForm').validate({
                rules: {
                    name: {
                        required: true,
                        uniqueName: true,
                    },
                    amount: {
                        required: true,
                        number: true
                    },
                    symbol: {
                        required: true,
                    },

                },
                messages: {
                    name: {
                        required: "يرجى إدخال الاسم من فضلك.",
                        uniqueName: 'هذا الاسم موجود بالفعل في النظام.'
                    },
                    amount: {
                        required: "يرجى ادخال قيمة العمله من فضلك",
                        'number': "يجب ان تكون القيمة رقما"
                    },
                    symbol: {
                        required: "يجب ادخال رمز العملة من فضلك"
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

            $('#edit_curencey').validate({
                rules: {
                    name: {
                        required: true,
                        uniqueName: true,
                    },
                    amount: {
                        required: true,
                        number: true
                    },
                    symbol: {
                        required: true,
                    },

                },
                messages: {
                    name: {
                        required: "يرجى إدخال الاسم من فضلك.",
                        uniqueName: 'هذا الاسم موجود بالفعل في النظام.'
                    },
                    amount: {
                        required: "يرجى ادخال قيمة العمله من فضلك",
                        'number': "يجب ان تكون القيمة رقما"
                    },
                    symbol: {
                        required: "يجب ادخال رمز العملة من فضلك"
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

            $.validator.addMethod("uniqueName", function(value, element) {
                if (value == '') {
                    return isValid = true;
                }
                let isValid = false;
                $.ajax({
                    url: '{{ route('curencey.checkName') }}', // مسار التحقق من الباركود
                    data: {
                        name: value,
                        id: $('#curencey_id').val()
                    },
                    type: 'GET',
                    async: false, // تأكد من أن التحقق يتم بشكل متزامن
                    success: function(response) {
                        isValid = response.isUnique; // إذا كانت النتيجة صحيحة (فريد)
                    }
                });
                return isValid;
            }, "هذا الاسم موجود بالفعل في النظام.");

            $('#curenceyForm,#edit_curencey').on('submit', function(e) {
                cleanNumbersBeforeSubmit();
            });


            attachCommaFormatter('.amount');

            function attachCommaFormatter(selector) {

                document.querySelectorAll(selector).forEach(input => {

                    input.addEventListener('input', function() {

                        /* ـــ حالة المؤشر قبل التعديل ـــ */
                        const oldPos = this.selectionStart;
                        const oldVal = this.value;

                        /* 1) إزالة كل ما ليس رقمًا أو نقطة */
                        let cleaned = oldVal.replace(/[^0-9.]/g, '');

                        /* 2) الاحتفاظ بأول نقطة فقط ومسح البقية */
                        const firstDot = cleaned.indexOf('.');
                        if (firstDot !== -1) {
                            cleaned = cleaned.slice(0, firstDot + 1) +
                                cleaned.slice(firstDot + 1).replace(/\./g, '');
                        }

                        /* 3) تقسيم إلى جزء صحيح وعشرى */
                        const [rawInt = '', rawDec = ''] = cleaned.split('.');

                        /* 4) إضافة فواصل للجزء الصحيح */
                        const intPart = rawInt.replace(/^0+(?!$)/, ''); // أصفار زائدة
                        const intWithCommas = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                        /* 5) إعادة التجميع */
                        let formatted = intWithCommas;
                        if (firstDot !== -1) {
                            // لو المستخدم كتب نقطة ولم يكتب أرقام بعدها نحافظ على النقطة
                            formatted += '.' + rawDec;
                        }

                        this.value = formatted;

                        /* 6) إعادة المؤشر لموضعه الطبيعى */
                        const diff = formatted.length - oldVal.length;
                        this.setSelectionRange(oldPos + diff, oldPos + diff);
                    });

                });
            }

            function cleanNumbersBeforeSubmit() {
                let singleFields = ['amount', 'amount_ed'];
                singleFields.forEach(id => {
                    let el = document.getElementById(id);
                    if (el && el.value) {
                        el.value = el.value.replace(/,/g, '');
                    }
                });
            }


        });
    </script>
@endsection
