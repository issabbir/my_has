@extends('layouts.auth')
@section('title')
    Reset Password
@endsection

@section('content') 
<section class="row flexbox-container">
    <div class="col-xl-7 col-10">
      <div class="row m-0">
        <!-- left section-login -->			  
          <div class="col-md-6 col-12 px-0 bg-rgba-cblack"> 
           <form class="" action="" method="post">
              <div class="card-header pb-0">
                  <div class="card-title">
                      <img src="{{asset('/assets/images/logo/cpa-logo.png')}}" alt="users view avatar" class="img-fluid mx-auto d-block">
                      <h4 class="text-center mt-1 text-white">CPA Portal</h4>
                      <h4 class="text-center mt-1 text-white">House Allotment System</h4>
                  </div>
              </div>
              <div class="card-content">
                      <div class="card-body pb-0">
                          <div class="divider">
                              <div class="divider-text text-uppercase text-light bg-transparent"><small>Reset password</small></div>
                          </div>
                          @if ($errors->has('password'))
                              <div class="alert alert-dismissible alert-danger">
                                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                                  <strong></strong> {{ $errors->first('password') }}
                              </div>
                          @endif
                          @if ($errors->has('cpassword'))
                              <div class="alert alert-dismissible alert-danger">
                                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                                  <strong></strong> {{ $errors->first('cpassword') }}
                              </div>
                          @endif
                          <div class="row ml-0 mr-0 p-0 bg-rgba-cwhite">
                              <div class="col-md-12 text-white">
                                  <div class="text-white">
                                      <div> <strong> Password rule </strong></div>
                                      <div> # Characters (a-zA – Z) at least one upper and lower </div>
                                      <div> # Base 10 digits (0 – 9) at least one </div>
                                      <div> # At least one Non-alphanumeric (!, $, #, or %) </div>
                                      <div> # Unicode characters </div>
                                  </div>
                              </div>
                          </div>

                      <div class="form-group">
                          <label class="text-bold-600 text-white" for="cpassword">Current password</label>
                          <input type="password" name="cpassword" class="form-control" id="cpassword" placeholder="********">
                      </div>
                      <div class="form-group">
                          <label class="text-bold-600 text-white" for="password">Password</label>
                          <input type="password" name="password" class="form-control" id="password" placeholder="********">
                      </div>
                      <div class="form-group">
                          <label class="text-bold-600 text-white" for="confirmPassword">Confirm password</label>
                          <input type="password" name="password_confirmation" class="form-control" id="confirmPassword" placeholder="********">
                      </div>
                  </div>
              </div>
              <div class="card-content">
                  <div class="card-body">	
                      <button type="submit" class="btn btn-primary glow position-relative w-100">
                          RESET<!--i id="icon-arrow" class="bx bx-right-arrow-alt"></i-->
                      </button>
                      <hr>
                      <div class="text-center">
                          <a class="dropdown-item" href="{{ route('logout', [app()->getLocale()]) }}"
                             onclick="event.preventDefault();
                                                                 document.getElementById('logout-form').submit();">
                              {{ __('Logout') }}
                          </a>
                      </div>
                  </div>
                  <div class="float-right text-light">
                      <small>Operation and Maintenance by</small>
                      <a class="text-primary font-weight-bold" href="https://site.cnsbd.com" target="_blank">
                          <img src="{{asset('/assets/images/logo/cns-logo-w.png')}}" alt="cns_logo" class="img-fluid mb-1"/>
                      </a>
                  </div>							
              </div>
               {!! csrf_field() !!}
           </form>
              <form id="logout-form" action="{{ route('logout',[app()->getLocale()]) }}" method="POST" style="display: none;">
                  @csrf
              </form>
          </div>					
      </div>
    </div>
  </section>
@endsection 