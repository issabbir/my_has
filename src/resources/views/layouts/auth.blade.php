<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="author" content="CNS LIMITED">
	<title>CPA Integrated System :: @yield('title')</title>
	<link rel="apple-touch-icon" href="{{asset('assets/images/ico/apple-icon-120.html')}}">
	<link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/images/logo/cns_favicon.png')}}">
	<link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">

	<!-- BEGIN: Vendor CSS-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/vendors/css/vendors.min.css')}}">
	<!-- END: Vendor CSS-->

	<!-- BEGIN: Theme CSS-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap-extended.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/colors.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/components.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/themes/dark-layout.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/themes/semi-dark-layout.min.css')}}">
	<!-- END: Theme CSS-->

	<!-- BEGIN: Page CSS-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/core/menu/menu-types/vertical-menu.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/pages/authentication.css')}}">
	<!-- END: Page CSS-->

	<!-- BEGIN: Custom CSS-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">

	<style>

		.bg-rgba-cblack{
			background: rgba(0, 0, 0, 0.44);
		}
		.bg-rgba-cwhite {
			background-color: rgba(193, 210, 201, 0.51);
		}

	</style>

	<!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<body class="vertical-layout vertical-menu-modern 1-column  navbar-sticky footer-static blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column" style="background: url({{asset('/assets/images/pages/login-bg.jpg')}}) center center no-repeat; background-size: cover;">
<!-- BEGIN: Content-->
<div class="app-content content">
	<div class="content-wrapper">
		@yield('content');
	</div>
</div>
<!-- END: Content-->


<!-- BEGIN: Vendor JS-->
<script src="{{asset('assets/vendors/js/vendors.min.js')}}"></script>
<script src="{{asset('assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.min.js')}}"></script>
<script src="{{asset('assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.min.js')}}"></script>
<script src="{{asset('assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js')}}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{asset('assets/js/scripts/configs/vertical-menu-light.min.js')}}"></script>
<script src="{{asset('assets/js/core/app-menu.min.js')}}"></script>
<script src="{{asset('assets/js/core/app.min.js')}}"></script>
<script src="{{asset('assets/js/scripts/components.min.js')}}"></script>
<script src="{{asset('assets/js/scripts/footer.min.js')}}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>
