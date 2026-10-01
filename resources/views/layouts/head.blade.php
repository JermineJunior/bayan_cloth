<!-- Title -->
<title> @yield('title') </title>
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

    /* ===== Product search side panel (product-pick-item) =====
       List version of the old bordered product table.
       Scoped with #body_search so it always wins over the .btn / .btn-primary
       bootstrap rules that the markup still carries for the JS state toggles. */
    #body_search {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: 10px;
        max-height: 60vh;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    #body_search::-webkit-scrollbar {
        width: 6px;
    }

    #body_search::-webkit-scrollbar-thumb {
        background: #c7d0de;
        border-radius: 3px;
    }

    .dark-theme #body_search::-webkit-scrollbar-thumb {
        background: #3d4a66;
    }

    #body_search .product-pick-item {
        display: block;
        width: 100%;
        margin: 0;
        text-align: right;
        padding: 10px 12px;
        border: 1px solid #e3e8ef;
        border-radius: 8px;
        background: #f6f9fe;
        color: #1a2231;
        font-size: 14px;
        line-height: 1.5;
        text-decoration: none;
        cursor: pointer;
        transition: background-color .15s ease, border-color .15s ease, color .15s ease;
    }

    #body_search .product-pick-item:hover,
    #body_search .product-pick-item:focus {
        background: #e8f1fe;
        border-color: #0162e8;
        color: #0b3f92;
        text-decoration: none;
    }

    #body_search .product-pick-item:active {
        background: #d7e8fd;
    }

    /* already added / disabled state - classes are toggled by the existing JS */
    #body_search .product-pick-item.btn-white,
    #body_search .product-pick-item.disabled {
        background: #f1f3f6;
        border-color: #e3e8ef;
        color: #98a2b3;
        cursor: not-allowed;
    }

    #body_search .product-pick-item.btn-white:hover,
    #body_search .product-pick-item.disabled:hover {
        background: #f1f3f6;
        border-color: #e3e8ef;
        color: #98a2b3;
    }

    /* optional muted value shown on the far side of the item */
    #body_search .product-pick-qty {
        float: left;
        font-size: 12px;
        color: #6c757d;
    }

    /* ===== gradient variant (product-pick-item--grad) =====
       Equivalent of Tailwind/BS5 `bg-gradient-to-r from-blue-600 to-teal-400`,
       i.e. to-right #2563eb -> teal. Those utility classes do not exist in this
       Bootstrap 4 build, so the gradient is declared directly.
       The teal stop is darkened to teal-700 (#0f766e) instead of teal-400
       (#2dd4bf) so white text clears WCAG AA (5.5:1 vs 1.9:1). */

    #body_search .product-pick-item--grad {
        background: linear-gradient(to right, #2563eb 0%, #0f766e 100%);
        border-color: #1a5fd0;
        color: #fff;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, .28);
    }

    #body_search .product-pick-item--grad:hover,
    #body_search .product-pick-item--grad:focus {
        background: linear-gradient(to right, #1d4ed8 0%, #115e59 100%);
        border-color: #1d4ed8;
        color: #fff;
        font-weight: 600;
        text-shadow: 0 1px 3px rgba(0, 0, 0, .35);
    }

    #body_search .product-pick-item--grad .product-pick-qty {
        color: rgba(255, 255, 255, .9);
        font-weight: 600;
    }

    #body_search .product-pick-item--grad.btn-white,
    #body_search .product-pick-item--grad.disabled {
        background: linear-gradient(to right, #f4f5f7 0%, #eceff3 100%);
        border-color: #e3e8ef;
        color: #98a2b3;
        font-weight: 400;
        text-shadow: none;
    }

    #body_search .product-pick-item--grad.btn-white .product-pick-qty,
    #body_search .product-pick-item--grad.disabled .product-pick-qty {
        color: #aeb6c2;
        font-weight: 400;
    }

    #body_search .product-pick-item--grad.btn-white:hover,
    #body_search .product-pick-item--grad.disabled:hover {
        background: linear-gradient(to right, #f4f5f7 0%, #eceff3 100%);
        border-color: #e3e8ef;
        color: #98a2b3;
    }

    /* gradient variant - dark theme */
    .dark-theme #body_search .product-pick-item--grad {
        background: linear-gradient(to right, #2563eb 0%, #0f766e 100%);
        border-color: #2563eb;
    }

    .dark-theme #body_search .product-pick-item--grad:hover,
    .dark-theme #body_search .product-pick-item--grad:focus {
        background: linear-gradient(to right, #1d4ed8 0%, #115e59 100%);
        border-color: #3d92ff;
    }

    .dark-theme #body_search .product-pick-item--grad .product-pick-qty {
        color: rgba(255, 255, 255, .9);
    }

    .dark-theme #body_search .product-pick-item--grad.btn-white,
    .dark-theme #body_search .product-pick-item--grad.disabled {
        background: linear-gradient(to right, #232c40 0%, #1f2940 100%);
        border-color: #2f3b57;
        color: #6b7688;
        font-weight: 400;
        text-shadow: none;
    }

    /* dark theme */
    .dark-theme #body_search .product-pick-item {
        background: #26314a;
        border-color: #2f3b57;
        color: #e6ebf2;
    }

    .dark-theme #body_search .product-pick-item:hover,
    .dark-theme #body_search .product-pick-item:focus {
        background: #1b2740;
        border-color: #0162e8;
        color: #fff;
    }

    .dark-theme #body_search .product-pick-item.btn-white,
    .dark-theme #body_search .product-pick-item.disabled {
        background: #1f2940;
        border-color: #2f3b57;
        color: #6b7688;
    }

    .dark-theme #body_search .product-pick-qty {
        color: #98a2b3;
    }

    .unread {
    background-color: #f0f8ff; /* لون خلفية مميز */
    border-left: 5px solid #0022ff; /* إضافة خط جانبي لتمييز الإشعار غير المقروء */

    .main-header-notification.show .dropdown-menu{
        overflow:  auto !important;
    }
}
</style>
