@yield('css')

<!-- Bootstrap Css -->
<link href="{{ URL::asset('/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ URL::asset('/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Toaster Css-->
<link href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}" id="toastr-style" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{ URL::asset('/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<!-- Custom Css-->
<link href="{{ URL::asset('/assets/css/custom.css') }}?{{time()}}" id="app-custom-style" rel="stylesheet" type="text/css" />
