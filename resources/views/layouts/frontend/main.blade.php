<!doctype html>
<html class="wide wow-animation" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="@yield('description', config('app.name'))" name="description" />
    <meta content="@yield('author', config('app.name'))" name="author" />
    <meta content="@yield('keywords', config('app.name'))" name="keywords" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">
    @include('layouts.frontend.head-css')
    <style>
        .ie-panel {
            display: none;
            background: #212121;
            padding: 10px 0;
            box-shadow: 3px 3px 5px 0 rgba(0, 0, 0, .3);
            clear: both;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        html.ie-10 .ie-panel,
        html.lt-ie-10 .ie-panel {
            display: block;
        }
    </style>
</head>

@section('body')

    <body data-sidebar="light" data-layout-mode="light">
	<div class="comingsoon d-none">
		<div class="content">
			<div class="title">COMING SOON</div>
			<div class="text">Please use the contact form to receive information about the official launch.</div>
			<div class="btn-container">
				<a href="#contacts" class="button button-primary button-ujarak">Contact Us</a>
			</div>
		</div>
	</div>
    @show
    <div class="ie-panel"><a href="http://windows.microsoft.com/en-US/internet-explorer/"><img
                src="images/ie8-panel/warning_bar_0000_us.jpg" height="42" width="820"
                alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today."></a>
    </div>
    <div class="preloader">
        <div class="preloader-body">
            <div class="cssload-container"><span></span><span></span><span></span><span></span>
            </div>
        </div>
    </div>
    <!-- Begin page -->
    <div id="layout-wrapper">
        {{-- @include('layouts.frontend.topbar') --}}
        @include('layouts.frontend.header')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="page main-content">
            @yield('content')
            <!-- End Page-content -->
            @include('layouts.frontend.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    <!-- Global Mailform Output-->
    <div class="snackbars" id="form-output-global"></div>
    <!-- JAVASCRIPT -->
    @include('layouts.frontend.vendor-scripts')

</body>

</html>
