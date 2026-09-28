@extends('layouts.app')

@section('title', 'بيانات المؤسسة')

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h5 class="content-title mb-0 my-auto"> بيانات المؤسسة</h5><span class="text-muted mt-1 tx-13 mr-2 mb-0">
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
                    {{-- <p class="tx-12 tx-gray-500 mb-2">Example of Valex Simple Table. <a href="">Learn more</a></p>  --}}
                </div>
                <div class="card-body">
                    <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <label for="name">اسم المؤسسة</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ $info->name }}" placeholder="اسم المؤسسة">
                                    @error('name')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <label for="email">البريد الإلكتروني للمؤسسة</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        value="{{ $info->email }}" placeholder="البريد الإلكتروني">
                                    @error('email')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <label for="location">عنوان المؤسسة</label>
                                    <input type="text" id="location" name="location" class="form-control"
                                        value="{{ $info->location }}" placeholder="عنوان المؤسسة">
                                    @error('location')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <label for="phone">رقم التلفون للمؤسسة</label>
                                    <input type="tel" id="phone" name="phone" class="form-control"
                                        value="{{ $info->phone }}" placeholder="رقم التلفون">
                                    @error('phone')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label mb-1">شعار المؤسسة</label>
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <img id="preview" style="max-height: 90px; max-width: 150px; background: #fff; padding: 6px;"
                                        class="border rounded"
                                        src="{{ $info->logo ? asset('image/logo/' . $info->logo) : Avatar::create('شعار') }}"
                                        alt="شعار المؤسسة">
                                    <div class="flex-grow-1">
                                        <input type="file" id="logo" name="logo" class="form-control"
                                            accept=".jpg,.jpeg,.png,.webp" onchange="previewImage(this)">
                                        <small class="text-muted d-block mt-1">
                                            الصيغ المسموحة: JPG, PNG, WEBP &mdash; الحد الأقصى للحجم 2 ميجابايت
                                        </small>
                                        @error('logo')
                                            <span class="text-danger"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save"></i> حفظ البيانات
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <script>
        function previewImage(event) {
            if (!event.target.files || !event.target.files[0]) {
                return;
            }
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
