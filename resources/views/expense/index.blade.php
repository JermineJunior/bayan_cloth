@extends('layouts.app')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    {{-- Select2 --}}
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> ادارة المصروفات</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    قائمة المصروفات</span>
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
                            @can('اضافة مصروف')
                                <a class="modal-effect btn btn-primary" data-effect="effect-super-scaled" data-toggle="modal"
                                    href="#addModal"><i class="fa fa-plus"> </i>&nbsp;&nbsp;إضافة مصروف
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
                                    <th class="wd-10p border-bottom-0">التاريخ</th>
                                    <th class="wd-15p border-bottom-0">الحساب</th>
                                    <th class="wd-15p border-bottom-0">البنك</th>
                                    <th class="wd-15p border-bottom-0">الخزنة</th>
                                    <th class="wd-10p border-bottom-0">المبلغ</th>
                                    <th class="wd-20p border-bottom-0">ملاحظات</th>
                                    <th class="wd-20p border-bottom-0">العمليات </th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($expenses as $expense)
                                    <tr data-id="{{ $expense->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $expense->madin->tree4_name }}</td>
                                        <td>{{ $expense->bankName->tree4_name ?? null }}</td>
                                        <td>{{ $expense->cach->tree4_name ?? null }}</td>
                                        <td>{{ number_format($expense->cach_name + $expense->bank_name, 2) }}</td>
                                        <td>{{ $expense->note }}</td>
                                        <td>
                                            @can('تعديل مصروف')
                                                <a class="modal-effect btn btn-outline-success btn-sm"
                                                    data-effect="effect-super-scaled" data-toggle="modal" href="#editModal"
                                                    data-id="{{ $expense->id }}" data-cach-id="{{ $expense->cach_id }}"
                                                    data-cach-name="{{ $expense->cach->tree4_name ?? null }}"
                                                    data-bank-id="{{ $expense->bank_id }}"
                                                    data-bank-name="{{ $expense->bankName->tree4_name ?? null }}"
                                                    data-madin-id="{{ $expense->madin_id }}" data-note="{{ $expense->note }}"
                                                    data-cach-price="{{ number_format($expense->cach_name, 2) }}"
                                                    data-bank-price="{{ number_format($expense->bank_name, 2) }}"
                                                    data-madin-name="{{ $expense->madin->tree4_name }}" title="تعديل">
                                                    <i class="fa fa-edit">
                                                    </i></a>
                                            @endcan
                                            @can('حذف مصروف')
                                                <a class="modal-effect btn btn-outline-danger btn-sm" data-target="#deleteModal"
                                                    data-toggle="modal" href="#deleteModal" data-id="{{ $expense->id }}"
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
                        <h6 class="modal-title">إضافة مصروف جديد </h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('expense.store') }}" method="post" id="formStore">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <span class="tx-danger">*</span><label for="madin">اسم الحساب</label>
                                <select name="madin" class="form-control" required style="width: 100%">
                                    <option value="">اختر الحساب</option>
                                    @foreach ($madins as $madin)
                                        <option value="{{ $madin->id }}">{{ $madin->tree4_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group row align-items-center">
                                {{-- المبلغ نقدا --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="dain_name[0]"
                                            class="form-control cach_price dain_name" placeholder="المبلغ نقدا">
                                    </div>
                                </div>

                                {{-- الخزنة --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <select name="dain_id[0]" class="form-control cach_id" style="width:100%">
                                            <option value="">-- اختر الخزنة --</option>
                                            @foreach ($safes as $safe)
                                                <option value="{{ $safe->id }}">{{ $safe->tree4_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                {{-- المبلغ بالبنك --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="dain_name[1]"
                                            class="form-control bank_price dain_name" placeholder="المبلغ بالبنك">
                                    </div>
                                </div>

                                {{-- اسم البنك --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <select name="dain_id[1]" class="form-control select2 bank_id"
                                            style="width: 100%;">
                                            <option value="">-- اختر البنك --</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->tree4_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="note">البيان</label>
                                <textarea name="note" class="form-control"></textarea>
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
                        <h6 class="modal-title">تعديل مصروف </h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="#" method="post" id="edit_expense">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <span class="tx-danger">*</span><label for="madin">اسم الحساب</label>
                                <select name="madin" class="form-control" id="madin" required style="width: 100%">
                                    <option value="">اختر الحساب</option>
                                    @foreach ($madins as $madin)
                                        <option value="{{ $madin->id }}">{{ $madin->tree4_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group row align-items-center">
                                {{-- المبلغ نقدا --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="dain_name[0]" id="cach_price"
                                            class="form-control cach_price dain_name" placeholder="المبلغ نقدا">
                                    </div>
                                </div>

                                {{-- الخزنة --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <select name="dain_id[0]" id="cach_id" class="form-control cach_id"
                                            style="width:100%">
                                            <option value="">-- اختر الخزنة --</option>
                                            @foreach ($safes as $safe)
                                                <option value="{{ $safe->id }}">{{ $safe->tree4_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                {{-- المبلغ بالبنك --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="dain_name[1]" id="bank_price"
                                            class="form-control bank_price dain_name" placeholder="المبلغ بالبنك">
                                    </div>
                                </div>

                                {{-- اسم البنك --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <select name="dain_id[1]" id="bank_id" class="form-control select2 bank_id"
                                            style="width: 100%;">
                                            <option value="">-- اختر البنك --</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->tree4_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="note">البيان</label>
                                <textarea name="note" class="form-control" id="note"></textarea>
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
                    <form action="expense/destroy" method="post" id="delete_expense">
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
    {{-- select2 --}}
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.form.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/additional-methods.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/form_validation/messages_ar.js') }}"></script>

    {{-- delete script  --}}
    <script>
        $('select').select2();
        $(document).ready(function() {

            $('#formStore').validate({
                rules: {
                    madin: {
                        required: true
                    },

                    // ❶ اسم الخزنة مطلوب في حالتين:
                    // - إذا أدخل المستخدم مبلغ نقداً
                    // - أو إذا لم يدخل أي شيء في باقي الحقول (بنك أو كاش)
                    'dain_id[0]': {
                        required: {
                            depends: function() {
                                const cashAmt = $('input[name="dain_name[0]"]').val().trim();
                                const bankAmt = $('input[name="dain_name[1]"]').val().trim();
                                const bankId = $('select[name="dain_id[1]"]').val();
                                return (cashAmt !== '') || (cashAmt === '' && bankAmt === '' &&
                                    bankId === '');
                            }
                        }
                    },

                    'dain_name[0]': {
                        required: {
                            depends: function() {
                                return $('select[name="dain_id[0]"]').val() !== '';
                            }
                        }
                    },

                    'dain_id[1]': {
                        required: {
                            depends: function() {
                                return $('input[name="dain_name[1]"]').val().trim() !== '';
                            }
                        }
                    },

                    'dain_name[1]': {
                        required: {
                            depends: function() {
                                return $('select[name="dain_id[1]"]').val() !== '';
                            }
                        }
                    }
                },

                messages: {
                    madin: {
                        required: "يرجى إدخال اسم الحساب"
                    },
                    'dain_id[0]': {
                        required: 'يرجى اختيار الخزنة'
                    },
                    'dain_name[0]': {
                        required: 'يرجى إدخال المبلغ نقداً'
                    },
                    'dain_id[1]': {
                        required: 'يرجى اختيار البنك'
                    },
                    'dain_name[1]': {
                        required: 'يرجى إدخال المبلغ بالبنك'
                    }
                },

                errorPlacement: function(error, element) {
                    if (element.closest('.input-group').length) {
                        error.insertAfter(element.closest('.input-group'));
                    } else if (element.hasClass('select2-hidden-accessible')) {
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
                },
                submitHandler: function(form) {

                    cleanNumbersBeforeSubmit();

                    let $submitBtn = $(form).find('button[type="submit"]');

                    $submitBtn.prop('disabled', true);

                    $submitBtn.html(`
                                        <span class="spinner-border spinner-border-sm"></span>
                                        جاري حفظ البيانات...
                                    `);

                    form.submit();
                }
            });

            $('#edit_expense').validate({
                rules: {
                    madin: {
                        required: true
                    },
                    note: {
                        required: true
                    },

                    'dain_id[0]': {
                        required: {
                            depends: function() {
                                const $f = $(this.form); // ← النموذج الحالي
                                const cashAmt = $f.find('input[name="dain_name[0]"]').val().trim();
                                const bankAmt = $f.find('input[name="dain_name[1]"]').val().trim();
                                const bankId = $f.find('select[name="dain_id[1]"]').val();
                                return (cashAmt !== '') || (cashAmt === '' && bankAmt === '' &&
                                    bankId === '');
                            }
                        }
                    },

                    'dain_name[0]': {
                        required: {
                            depends: function() {
                                const $f = $(this.form);
                                return $f.find('select[name="dain_id[0]"]').val() !== '';
                            }
                        }
                    },

                    'dain_id[1]': {
                        required: {
                            depends: function() {
                                const $f = $(this.form);
                                return $f.find('input[name="dain_name[1]"]').val().trim() !== '';
                            }
                        }
                    },

                    'dain_name[1]': {
                        required: {
                            depends: function() {
                                const $f = $(this.form);
                                return $f.find('select[name="dain_id[1]"]').val() !== '';
                            }
                        }
                    },

                },

                messages: {
                    madin: {
                        required: "يرجى إدخال اسم الحساب"
                    },
                    note: {
                        required: "يرجى إدخال الملاحظات"
                    },
                    'dain_id[0]': {
                        required: 'يرجى اختيار الخزنة'
                    },
                    'dain_name[0]': {
                        required: 'يرجى إدخال المبلغ نقداً'
                    },
                    'dain_id[1]': {
                        required: 'يرجى اختيار البنك'
                    },
                    'dain_name[1]': {
                        required: 'يرجى إدخال المبلغ بالبنك'
                    }
                },

                errorPlacement: function(error, element) {
                    if (element.closest('.input-group').length) {
                        error.insertAfter(element.closest('.input-group'));
                    } else if (element.hasClass('select2-hidden-accessible')) {
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

            // Handling data passing to edit and delete modals
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var cachId = button.data('cach-id');
                var cachName = button.data('cach-name');
                var cachPrice = button.data('cach-price');
                var bankPrice = button.data('bank-price');
                var bankId = button.data('bank-id');
                var bankName = button.data('bank-name');
                var madinId = button.data('madin-id');
                var madinName = button.data('madin-name');
                var note = button.data('note');
                var modal = $(this);
                $('#edit_expense').attr('action', '{{ route('expense.update', ':id') }}'.replace(':id',
                    id));

                var modal = $(this);
                $('#edit_expense').attr('action', '{{ route('expense.update', ':id') }}'.replace(':id',
                    id));
                if ($('#cach_id option[value="' + cachId + '"]').length === 0) {
                    var newCachOption = new Option(cachName, cachId, true, true);
                    $('#cach_id').append(newCachOption).trigger('change');
                } else {
                    $('#cach_id').val(cachId).trigger('change');
                }

                if ($('#bank_id option[value="' + bankId + '"]').length === 0) {
                    var newBankOption = new Option(bankName, bankId, true, true);
                    $('#bank_id').append(newBankOption).trigger('change');
                } else {
                    $('#bank_id').val(bankId).trigger('change');
                }

                if ($('#madin option[value="' + madinId + '"]').length === 0) {
                    var newMadinOption = new Option(madinName, madinId, true, true);
                    $('#madin').append(newMadinOption).trigger('change');
                } else {
                    $('#madin').val(madinId).trigger('change');
                }

                modal.find('.modal-body #cach_name').val(cachName);
                modal.find('.modal-body #bank_name').val(bankName);
                modal.find('.modal-body #cach_price').val(cachPrice).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                modal.find('.modal-body #bank_price').val(bankPrice).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                modal.find('.modal-body #note').val(note);

            });

            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var modal = $(this);
                $('#delete_expense').attr('action', '{{ route('expense.destroy', ':id') }}'.replace(
                    ':id', id));
            });

            document.getElementById('formStore').addEventListener('submit', function(e) {
                cleanNumbersBeforeSubmit();
            });
            document.getElementById('edit_expense').addEventListener('submit', function(e) {
                cleanNumbersBeforeSubmit();
            });

            attachCommaFormatter('.dain_name');

        });

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
            // الحقول ذات الأسماء المتكررة كمصفوفات (price[], row_sub_total[], qty[] ... الخ)
            // نفترض ان الفورم يحتوي على هذه الحقول، نمر عليها كلها
            document.querySelectorAll('input[name^="dain_name"]').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
        }
    </script>
@endsection
