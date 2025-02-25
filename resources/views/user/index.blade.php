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
                <h4 class="content-title mb-0 my-auto">المستخدمين</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة
                    المستخدمين</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
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
    @if ($errors->any())
        <div class="alert alert-outline-danger" role="alert">
            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                <span aria-hidden="true">&times;</span></button>
            @foreach ($errors->all() as $error)
                <strong>خطأ!</strong> {{ $error }}
                <br>
            @endforeach
        </div>
    @endif

    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="col-sm-1 col-md-2">
                        @can('اضافة مستخدم')
                            <a class="btn btn-primary" href="{{ route('user.create') }}">&nbsp;&nbsp;إضافة مستخدم</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="example1" data-page-length='50' style=" text-align: center;">
                            <thead>
                                <tr>
                                    <th class="wd-10p border-bottom-0">م</th>
                                    <th class="wd-15p border-bottom-0">اسم المستخدم</th>
                                    <th class="wd-20p border-bottom-0">البريد الالكتروني</th>
                                    <th class="wd-15p border-bottom-0">حالة المستخدم</th>
                                    <th class="wd-10p border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-{{ $user->status ? 'success' : 'danger' }} toggle-status-btn"
                                                data-id="{{ $user->id }}">
                                                {{ $user->status == 1 ? 'نشط' : 'غير نشط' }}
                                            </button>
                                        </td>
                                        <td>
                                            @can('تعديل مستخدم')
                                                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-success"
                                                    title="تعديل"><i class="fa fa-edit"></i></a>
                                            @endcan

                                            @can('تعيين كلمة المرور')
                                                <a href="#" id="reset" class="btn btn-sm btn-info reset-password"
                                                    title="تعيين كمة المرور" data-user-id="{{ $user->id }}"><i
                                                        class="fa fa-key"></i></a>
                                            @endcan

                                            @can('حذف مستخدم')
                                                <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                    data-id="{{ $user->id }}" data-toggle="modal" href="#deleteModal"
                                                    title="حذف"><i class="fa fa-trash"></i></a>
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

        {{-- // modal delete alert    --}}
        <div class="modal" id="deleteModal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content tx-size-sm">
                    <div class="modal-body tx-center pd-y-20 pd-x-20">
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                                aria-hidden="true">&times;</span></button> <i
                            class="icon icon ion-ios-close-circle-outline tx-100 tx-danger lh-1 mg-t-20 d-inline-block"></i>
                        <form id="deleteForm" action="{{ route('user.destroy', 'test') }}" method="post">
                            @csrf
                            @method('DELETE')
                            <h4 class="tx-danger mg-b-20">هل تريد حذف هذا السجل بالفعل ؟</h4>
                            {{-- <input type="text" id="section_name" readonly  name="section_name" class="form-control"  >  --}}
                            <p class="mg-b-20 mg-x-20">في حالة الموافقة لا يمكنك التراجع عن هذا الاجراء !</p>
                            <button class="btn ripple btn-danger pd-x-25" type="submit">نعم , حذف السجل</button>
                            <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">إلغاء</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    </div>
    <!-- /row -->
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
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <!-- Internal Modal js-->
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>

    <script>
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            
            var modal = $(this);
            $('#deleteForm').attr('action', '{{ route('user.destroy', ':id') }}'.replace(':id', id));
        });

        document.querySelectorAll('.reset-password').forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                // جلب user_id من السمة data-user-id
                let user_id = this.getAttribute('data-user-id');
                console.log(user_id);

                const url = '{{ route('user.reset.password') }}';

                // إرسال طلب AJAX
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: user_id,
                    },
                    success: function(response) {
                        notif({
                            msg: 'تم إعادة كلمة المرور بنجاح',
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
        });

        $('.toggle-status-btn').click(function() {
            var id = $(this).data('id');
            var button = $(this);

            $.ajax({
                type: 'POST',
                url: '{{ route('user.toggleStatus', '') }}/' + id,
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
    </script>
@endsection
