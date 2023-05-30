@extends('layouts.auth')

{{--@section('content')
	<section class="row flexbox-container">
		<div class="col-xl-7 col-10">
			<div class="row m-0">
				<!-- left section-login -->
				<div class="col-md-6 col-12 px-0 bg-rgba-cblack">
					<form class="" method="post" action="{{url('/authorization/login')}}">
						{{ csrf_field() }}
						<div class="card-header pb-0">
							<div class="card-title">
								<img src="{{asset('assets/images/logo/cpa-logo.png')}}" alt="users view avatar" class="img-fluid mx-auto d-block">
								<h4 class="text-center mt-1 text-white">CPA Portal</h4>
							</div>
						</div>
						<div class="card-content">
							<div class="card-body">

								<div class="form-group">
									<label class="text-bold-600 text-white" for="exampleInputPassword1">Username *</label>
									<input type="text" class="form-control" id="exampleInputPassword1" placeholder="Username" required name="p_user_name">
								</div>
								<div class="form-group">
									<label class="text-bold-600 text-white" for="exampleInputPassword2">Password *</label>
									<input type="password" class="form-control" id="exampleInputPassword2" placeholder="Password" required name="p_user_pass">
								</div>
								<div class="form-group">
									<div class="checkbox checkbox-sm">
										<input type="checkbox" class="form-check-input" id="exampleCheck1">
										<label class="checkboxsmall text-white" for="exampleCheck1"><small>Keep me logged in</small></label>
									</div>
								</div>

							</div>
						</div>


						<div class="card-content">
							<div class="card-body">
								<button type="submit" class="btn btn-primary glow position-relative w-100">
									LOGIN
								</button>
								<hr>
								<div class="text-center">
									<a href="forgot_pass.html" class="text-light"><small>Forgot Password?</small></a>
								</div>
							</div>
							<div class="float-right text-light">
								<small>Operation and Maintenance by</small>
								<a class="text-primary font-weight-bold " href="https://site.cnsbd.com" target="_blank">
									<img src="{{asset('assets/images/logo/cns-logo-w.png')}}" alt="cns_logo" class="img-fluid mb-1"/>
								</a>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div>
	</section>
@endsection--}}

@section('content')
	<section class="row flexbox-container">
		<div class="col-xl-7 col-10">
			<div class="row m-0">
				<!-- left section-login -->
				<div class="col-md-6 col-12 px-0 bg-rgba-cblack">
					<form class="" action="{{ route('authorization.login') }}" method="post">
						<div class="card-header pb-0">
							<div class="card-title">
								<img src="{{asset('/assets/images/logo/cpa-logo.png')}}" alt="users view avatar" class="img-fluid mx-auto d-block">
								<h4 class="text-center mt-1 text-white">CPA Portal</h4>
								<h4 class="text-center mt-1 text-white">House Allotment System</h4>
							</div>
						</div>
						<div class="card-content">
							<div class="card-body">
								@if ($errors->has('error'))
									<div class="alert alert-dismissible alert-danger">
										<button type="button" class="close" data-dismiss="alert">&times;</button>
										<strong></strong> {{ $errors->first('error') }}
									</div>
								@endif
								@if (session()->has('message'))
									<div class="alert alert-dismissible alert-success">
										<button type="button" class="close" data-dismiss="alert">&times;</button>
										<strong></strong> {{ session()->get('message') }}
									</div>
								@endif
								<div class="form-group">
									<label class="text-bold-600 text-white" for="exampleInputPassword1">Username *</label>
									<input type="text" class="form-control" name="p_user_name" id="exampleInputPassword1" placeholder="Username" required>
								</div>
								<div class="form-group">
									<label class="text-bold-600 text-white" for="exampleInputPassword2">Password *</label>
									<input type="password" class="form-control" id="exampleInputPassword2" placeholder="Password" name="p_user_pass" required>
								</div>
								<div class="form-group">
									<div class="checkbox checkbox-sm">
										<input type="checkbox" name="rememberMe" class="form-check-input" id="exampleCheck1">
										<label class="checkboxsmall text-white" for="exampleCheck1"><small>Keep me logged in</small></label>
									</div>
								</div>
							</div>
						</div>

						<div class="card-content">
							<div class="card-body">
								<button type="submit" class="btn btn-primary glow position-relative w-100">
									LOGIN<!--i id="icon-arrow" class="bx bx-right-arrow-alt"></i-->
								</button>
								<hr>
								<div class="text-center">
									<a href="/forgot-password" class="text-light"><small>Forgot Password?</small></a>
								</div>
							</div>
							<div class="float-right text-light">
								<small>Operation and Maintenance by</small>
								<a class="text-primary font-weight-bold " href="https://site.cnsbd.com" target="_blank">
									<img src="{{asset('/assets/images/logo/cns-logo-w.png')}}" alt="cns_logo" class="img-fluid mb-1"/>
								</a>
							</div>
						</div>
						{!! csrf_field() !!}
					</form>
				</div>
			</div>
		</div>
	</section>
@endsection
