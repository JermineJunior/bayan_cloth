<!-- Title -->
<title> B2B - Travel Sudan </title>
<!-- Favicon -->
<link rel="icon" href="{{ asset('image/logo/' . GeneralSiteSettings('logo')) }}" type="image/x-icon" />
<!-- Icons css -->
<link href="{{ URL::asset(path: 'assets/css/icons.css') }}" rel="stylesheet">
<!--  Custom Scroll bar-->
<link href="{{ URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.css') }}" rel="stylesheet" />
<!--  Sidebar css -->
<link href="{{ URL::asset('assets/plugins/sidebar/sidebar.css') }}" rel="stylesheet">
<!-- Sidemenu css -->
<link rel="stylesheet" href="{{ URL::asset('assets/css-rtl/sidemenu.css') }}">
@yield('css')
<!--- Style css -->
<link href="{{ URL::asset('assets/css-rtl/style.css') }}" rel="stylesheet">
<!--- Dark-mode css -->
<link href="{{ URL::asset('assets/css-rtl/style-dark.css') }}" rel="stylesheet">
<!---Skinmodes css-->
<link href="{{ URL::asset('assets/css-rtl/skin-modes.css') }}" rel="stylesheet">
{{--  font family  --}}
<link href="{{ asset('assets/font/test.css') }}" rel="stylesheet">
{{--  font family  --}}
<style>
    * {
        font-family: "Noto Kufi Arabic", sans-serif;
    }

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_filter {
        width: 50%;
        float: right;
        text-align: right;
    }

    div.dataTables_wrapper div.dataTables_filter label {
        font-weight: normal;
        white-space: nowrap;
        text-align: left;
        margin-right: 50px;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0.5em;
        display: inline-block;
        width: 198px;
    }

    .sidebar-footer a {
        width: 33.3333%;
    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    .swal-title {
        color: black;
    }

    .swal-footer {
        text-align: center;
        direction: ltr;
    }

    .swal2-popup.swal2-toast.swal2-show {
        width: 100%;
        font-size: 15px;
    }

    .swal-text {
        text-align: center
    }

    .unread {
    background-color: #f0f8ff; /* لون خلفية مميز */
    border-left: 5px solid #0022ff; /* إضافة خط جانبي لتمييز الإشعار غير المقروء */

    .main-header-notification.show .dropdown-menu{
        overflow:  auto !important;
    }
}
</style>
