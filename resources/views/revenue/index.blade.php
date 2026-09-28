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

    <style>
        .text-danger {
            font-size: 13px;
            display: block;
            margin-top: 5px;
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> ادارة الايردادات</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    قائمة الايردادات</span>
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
                            @can('اضافة ايراد')
                                <a class="modal-effect btn btn-primary" data-effect="effect-super-scaled" data-toggle="modal"
                                    href="#addModal"><i class="fa fa-plus"> </i>&nbsp;&nbsp;إضافة ايراد
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
                                    <th class="wd-10p border-bottom-0">#</th>
                                    <th class="wd-15p border-bottom-0">الحساب</th>
                                    <th class="wd-15p border-bottom-0">البنك</th>
                                    <th class="wd-15p border-bottom-0">الخزنة</th>
                                    <th class="wd-10p border-bottom-0">المبلغ</th>
                                    <th class="wd-20p border-bottom-0">ملاحظات</th>
                                    <th class="wd-20p border-bottom-0">العمليات </th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($revenues as $revenue)
                                    <tr data-id="{{ $revenue->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $revenue->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $revenue->dain->tree4_name }}</td>
                                        <td>{{ $revenue->bankName->tree4_name ?? null }}</td>
                                        <td>{{ $revenue->cach->tree4_name ?? null }}</td>
                                        <td>{{ number_format($revenue->cach_name + $revenue->bank_name, 2) }}</td>
                                        <td>{{ $revenue->note }}</td>
                                        <td>
                                            @can('تعديل ايراد')
                                                <a class="modal-effect btn btn-outline-success btn-sm"
                                                    data-effect="effect-super-scaled" data-toggle="modal" href="#editModal"
                                                    data-id="{{ $revenue->id }}" data-cach-id="{{ $revenue->cach_id }}"
                                                    data-cach-name="{{ $revenue->cach->tree4_name ?? null }}"
                                                    data-bank-id="{{ $revenue->bank_id }}"
                                                    data-bank-name="{{ $revenue->bankName->tree4_name ?? null }}"
                                                    data-dain-id="{{ $revenue->dain_id }}" data-note="{{ $revenue->note }}"
                                                    data-cach-price="{{ number_format($revenue->cach_name, 2) }}"
                                                    data-bank-price="{{ number_format($revenue->bank_name, 2) }}"
                                                    data-transaction-no="{{ $revenue->transaction_no }}"
                                                    data-dain-name="{{ $revenue->dain->tree4_name }}" title="تعديل">
                                                    <i class="fa fa-edit">
                                                    </i></a>
                                            @endcan
                                            @can('حذف ايراد')
                                                <a class="modal-effect btn btn-outline-danger btn-sm" data-target="#deleteModal"
                                                    data-toggle="modal" href="#deleteModal" data-id="{{ $revenue->id }}"
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
                        <h6 class="modal-title">إضافة ايراد جديد </h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('revenue.store') }}" method="post" id="formStore">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <span class="tx-danger">*</span><label for="dain">اسم الحساب</label>
                                <select name="dain" class="form-control dain" required style="width: 100%">
                                    <option value="">اختر الحساب</option>
                                    @foreach ($dains as $dain)
                                        <option value="{{ $dain->id }}">{{ $dain->tree4_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group row align-items-center">
                                {{-- المبلغ نقدا --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="madin_name[0]"
                                            class="form-control cach_price madin_name" placeholder="المبلغ نقدا">
                                    </div>
                                </div>

                                {{-- الخزنة --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <select name="madin_id[0]" class="form-control cach_id" style="width:100%">
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
                                        <input type="text" name="madin_name[1]"
                                            class="form-control bank_price madin_name" placeholder="المبلغ بالبنك"
                                            id="bank_price_add">
                                    </div>
                                </div>

                                {{-- رقم العملية --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <input type="text" name="transaction_no" id="transaction_no_add"
                                            class="form-control" placeholder="رقم العملية" style="display:none;">
                                    </div>
                                </div>

                                {{-- اسم البنك --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <select name="madin_id[1]" class="form-control select2 bank_id"
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
                                <label for="dain">البيان</label>
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
                        <h6 class="modal-title">تعديل ايراد </h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="#" method="post" id="edit_revenue">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <span class="tx-danger">*</span><label for="dain">اسم الحساب</label>
                                <select name="dain" class="form-control" id="dain" required style="width: 100%">
                                    <option value="">اختر الحساب</option>
                                    @foreach ($dains as $dain)
                                        <option value="{{ $dain->id }}">{{ $dain->tree4_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group row align-items-center">
                                {{-- المبلغ نقدا --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="madin_name[0]" id="cach_price"
                                            class="form-control cach_price madin_name" placeholder="المبلغ نقدا">
                                    </div>
                                </div>

                                {{-- الخزنة --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <select name="madin_id[0]" id="cach_id" class="form-control cach_id"
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
                                        <input type="text" name="madin_name[1]" id="bank_price"
                                            class="form-control bank_price madin_name" placeholder="المبلغ بالبنك">
                                    </div>
                                </div>

                                {{-- رقم العملية --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <input type="text" name="transaction_no" id="transaction_no"
                                            class="form-control" placeholder="رقم العملية" style="display:none;">
                                    </div>
                                </div>

                                {{-- اسم البنك --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <select name="madin_id[1]" id="bank_id" class="form-control select2 bank_id"
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
                    <form action="revenues/destroy" method="post" id="delete_revenue">
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
                    dain: {
                        required: true
                    },
                    'madin_id[0]': {
                        required: {
                            depends: function() {
                                const cashAmt = $('input[name="madin_name[0]"]').val().trim();
                                const bankAmt = $('input[name="madin_name[1]"]').val().trim();
                                const bankId = $('select[name="madin_id[1]"]').val();
                                return (cashAmt !== '') || (cashAmt === '' && bankAmt === '' &&
                                    bankId === '');
                            }
                        }
                    },

                    'madin_name[0]': {
                        required: {
                            depends: function() {
                                return $('select[name="madin_id[0]"]').val() !== '';
                            }
                        }
                    },

                    'madin_id[1]': {
                        required: {
                            depends: function() {
                                return $('input[name="madin_name[1]"]').val().trim() !== '';
                            }
                        }
                    },

                    'madin_name[1]': {
                        required: {
                            depends: function() {
                                return $('select[name="madin_id[1]"]').val() !== '';
                            }
                        }
                    },

                    'transaction_no': {
                        required: {
                            depends: function() {
                                const $f = $(this.form);
                                const unformat = v => parseFloat(String(v).replace(/,/g, '')) || 0;
                                return unformat($f.find('input[name="madin_name[1]"]').val()) > 0;
                            }
                        }
                    }
                },

                messages: {
                    dain: {
                        required: "يرجى إدخال اسم الحساب"
                    },
                    'madin_id[0]': {
                        required: 'يرجى اختيار الخزنة'
                    },
                    'madin_name[0]': {
                        required: 'يرجى إدخال المبلغ نقداً'
                    },
                    'madin_id[1]': {
                        required: 'يرجى اختيار البنك'
                    },
                    'madin_name[1]': {
                        required: 'يرجى إدخال المبلغ بالبنك'
                    },
                    'transaction_no': {
                        required: 'يرجى إدخال رقم عملية البنك'
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

            $('#edit_revenue').validate({
                rules: {
                    dain: {
                        required: true
                    },
                    note: {
                        required: true
                    },

                    'madin_id[0]': {
                        required: {
                            depends: function() {
                                const $f = $(this.form); // ← النموذج الحالي
                                const cashAmt = $f.find('input[name="madin_name[0]"]').val().trim();
                                const bankAmt = $f.find('input[name="madin_name[1]"]').val().trim();
                                const bankId = $f.find('select[name="madin_id[1]"]').val();
                                return (cashAmt !== '') || (cashAmt === '' && bankAmt === '' &&
                                    bankId === '');
                            }
                        }
                    },

                    'madin_name[0]': {
                        required: {
                            depends: function() {
                                const $f = $(this.form);
                                return $f.find('select[name="madin_id[0]"]').val() !== '';
                            }
                        }
                    },

                    'madin_id[1]': {
                        required: {
                            depends: function() {
                                const $f = $(this.form);
                                return $f.find('input[name="madin_name[1]"]').val().trim() !== '';
                            }
                        }
                    },

                    'madin_name[1]': {
                        required: {
                            depends: function() {
                                const $f = $(this.form);
                                return $f.find('select[name="madin_id[1]"]').val() !== '';
                            }
                        }
                    },

                    'transaction_no': {
                        required: {
                            depends: function() {
                                const $f = $(this.form);
                                const unformat = v => parseFloat(String(v).replace(/,/g, '')) || 0;
                                return unformat($f.find('input[name="madin_name[1]"]').val()) > 0;
                            }
                        }
                    }

                },

                messages: {
                    dain: {
                        required: "يرجى إدخال اسم الحساب"
                    },
                    note: {
                        required: "يرجى إدخال الملاحظات"
                    },
                    'madin_id[0]': {
                        required: 'يرجى اختيار الخزنة'
                    },
                    'madin_name[0]': {
                        required: 'يرجى إدخال المبلغ نقداً'
                    },
                    'madin_id[1]': {
                        required: 'يرجى اختيار البنك'
                    },
                    'madin_name[1]': {
                        required: 'يرجى إدخال المبلغ بالبنك'
                    },
                    'transaction_no': {
                        required: 'يرجى إدخال رقم عملية البنك'
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
                var dainId = button.data('dain-id');
                var dainName = button.data('dain-name');
                var note = button.data('note');
                var transactionNo = button.data('transaction-no');
                var modal = $(this);
                $('#edit_revenue').attr('action', '{{ route('revenue.update', ':id') }}'.replace(':id',
                    id));

                var modal = $(this);
                $('#edit_revenue').attr('action', '{{ route('revenue.update', ':id') }}'.replace(':id',
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

                if ($('#dain option[value="' + dainId + '"]').length === 0) {
                    var newDainOption = new Option(dainName, dainId, true, true);
                    $('#dain').append(newDainOption).trigger('change');
                } else {
                    $('#dain').val(dainId).trigger('change');
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
                modal.find('.modal-body #transaction_no').val(transactionNo || '');
                if (parseFloat(String(bankPrice).replace(/,/g, '')) > 0) {
                    modal.find('.modal-body #transaction_no').show();
                } else {
                    modal.find('.modal-body #transaction_no').hide().val('');
                }

            });

            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var modal = $(this);
                $('#delete_revenue').attr('action', '{{ route('revenue.destroy', ':id') }}'.replace(
                    ':id', id));
            });
            
            document.getElementById('edit_revenue').addEventListener('submit', function(e) {
                cleanNumbersBeforeSubmit();
            });

            attachCommaFormatter('.madin_name');

            $('#bank_price_add').on('input', function() {
                const unformat = v => parseFloat(String(v).replace(/,/g, '')) || 0;
                if (unformat($(this).val()) > 0) {
                    $('#transaction_no_add').show();
                } else {
                    $('#transaction_no_add').hide().val('');
                }
            });

            $('#bank_price').on('input', function() {
                const unformat = v => parseFloat(String(v).replace(/,/g, '')) || 0;
                if (unformat($(this).val()) > 0) {
                    $('#transaction_no').show();
                } else {
                    $('#transaction_no').hide().val('');
                }
            });


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
            document.querySelectorAll('input[name^="madin_name"]').forEach(el => {
                if (el.value) {
                    el.value = el.value.replace(/,/g, '');
                }
            });
        }
    </script>
@endsection
