<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords"
        content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4" />

    @include('layouts.head')
</head>

<body
    class="main-body app sidebar-mini {{ Route::is('sale_invoice.create') || Route::is('sale_invoice.edit') || Route::is('purchase_invoice.create') || Route::is('purchase_invoice.edit') || Route::is('sale_invoice_return.create') || Route::is('sale_invoice_return.edit') || Route::is('purchase_invoice_return.create') || Route::is('purchase_invoice_reutrn.edit') || Route::is('preliminaryInvoice.create') || Route::is('preliminaryInvoice.transform') ? 'sidenav-toggled' : '' }}">
    <!-- Loader -->
    <div id="global-loader">
        <img src="{{ URL::asset('assets/img/loader.svg') }}" class="loader-img" alt="Loader">
    </div>
    <!-- /Loader -->
    @include('layouts.main-sidebar')
    <!-- main-content -->
    <div class="main-content app-content">
        {{-- @if (
            (Route::is('sale_invoice.create') ||
                Route::is('sale_invoice.edit') ||
                Route::is('purchase_invoice.create') ||
                Route::is('purchase_invoice.edit') ||
                Route::is('sale_invoice_return.create') ||
                Route::is('sale_invoice_return.edit') ||
                Route::is('purchase_invoice_return.create') ||
                Route::is('purchase_invoice_reutrn.edit') ||
                Route::is('preliminaryInvoice.create') ||
                Route::is('preliminaryInvoice.transform')
            )) --}}
            @include('layouts.main-header')
        {{-- @endif --}}
        <!-- container -->
        <div class="container-fluid">
            @yield('page-header')
            @yield('content')
            @include('layouts.footer')
            @include('layouts.footer-scripts')
        </div>



</body>

</html>
