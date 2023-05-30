<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
	<div class="navbar-header">
		<ul class="nav navbar-nav flex-row">
			<li class="nav-item mr-auto">
				<a class="navbar-brand mt-0" href="#">
					<img src="{{asset('assets/images/logo/cpa-logo.png')}}" alt="users view avatar" class="img-fluid"/>
				</a>
			</li>
			<li class="nav-item nav-toggle">
				<a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
					<i class="bx bx-x d-block d-xl-none font-medium-4 primary"></i>
					<i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary" data-ticon="bx-disc"></i>
				</a>
			</li>
		</ul>
	</div>
	<div class="shadow-bottom"></div>
	<div class="main-menu-content mt-1">
		<ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">

			<!---Dashboard---->
			<li class="nav-item">
				<a href="#">
					<i class="bx bx-dashboard" data-icon="desktop"></i>
					<span class="menu-title" data-i18n="Dashboard">Dashboard</span>
				</a>
			</li>

			<li class=" navigation-header"><span>House Allotment</span></li>

			{{--<li class=" nav-item  has-sub">
				<a href="#">
					<i class="bx bx-notepad" data-icon="users"></i>
					<span class="menu-title" >Colony</span>
				</a>
				<ul class="menu-content">
					<li>
						<a href="{{url('/colony/colony-register')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">Register</span>
						</a>
					</li>

				</ul>
			</li>--}}

			<li class=" nav-item  has-sub">
				<a href="#">
					<i class="bx bx-notepad" data-icon="users"></i>
					<span class="menu-title" >Setup</span>
				</a>
				<ul class="menu-content">
					<li>
						<a href="{{url('/colony/colony-register')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">Colony</span>
						</a>
					</li>
					<li>
						<a href="{{url('/buildings')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">Building</span>
						</a>
					</li>
					<li>
						<a href="{{url('/houses')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">House</span>
						</a>
					</li>
                    <li>
                        <a href="{{url('/advertisements')}}">
                            <i class="bx bx-right-arrow-alt"></i>
                            <span class="menu-item">Advertisement</span>
                        </a>
                    </li>
				</ul>
			</li>
			<li class=" nav-item">
				<a href="{{url('/ha-applications')}}">
					<i class="bx bx-notepad" data-icon="users"></i>
					<span class="menu-item">Allotment Application</span>
				</a>
			</li>
			<li class=" nav-item">
				<a href="{{url('/point-assessments')}}">
					<i class="bx bx-notepad" data-icon="users"></i>
					<span class="menu-item">Point Assessment</span>
				</a>
			</li>
            <li class=" nav-item">
                <a href="{{url('/allotment-letter')}}">
                    <i class="bx bx-notepad" data-icon="users"></i>
                    <span class="menu-item">Allotment Letter Distribution</span>
                </a>
            </li>
{{--
			<li class=" nav-item  has-sub">
				<a href="#">
					<i class="bx bx-notepad" data-icon="users"></i>
					<span class="menu-title" >Register</span>
				</a>
				<ul class="menu-content">
					<li>
						<a href="{{url('/home')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">Agency</span>
						</a>
					</li>

					<li>
						<a href="{{url('/home')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">Stakeholder</span>
						</a>
					</li>
				</ul>
			</li>

			<li class=" nav-item  has-sub">
				<a href="#">
					<i class="bx bx-notepad" data-icon="users"></i>
					<span class="menu-title" >Chairman Visit</span>
				</a>
			</li>

			<li class=" nav-item  has-sub">
				<a href="#">
					<i class="bx bx-notepad" data-icon="users"></i>
					<span class="menu-title" >Appointment</span>
				</a>
				<ul class="menu-content">
					<li>
						<a href="{{url('/home')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">Request</span>
						</a>
					</li>

					<li>
						<a href="{{url('/home')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">Approval</span>
						</a>
					</li>
				</ul>
			</li>

			<li class=" nav-item  has-sub">
				<a href="#">
					<i class="bx bx-notepad" data-icon="users"></i>
					<span class="menu-title" >Activity Calender</span>
				</a>
			</li>

			<li class=" nav-item  has-sub">
				<a href="#">
					<i class="bx bx-notepad" data-icon="users"></i>
					<span class="menu-title" >Day Schedule</span>
				</a>
			</li> --}}

			<li class=" nav-item  has-sub">
				<a href="#">
					<i class="bx bx-notepad" data-icon="users"></i>
					<span class="menu-title" >Report</span>
				</a>
				<ul class="menu-content">
					<li>
						<a target="_blank" href="{{url('/report/render?xdo=/~weblogic/HAS/RPT_COLONY_LIST.xdo&type=pdf&filename=colony_list')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">Colony Register</span>
						</a>
                    </li>
					<li>
						<a target="_blank" href="{{url('/report/render?xdo=/~weblogic/HAS/RPT_HOUSE.xdo&type=pdf&filename=house_list')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">House List</span>
						</a>
					</li>
					<li>
						<a target="_blank" href="{{url('/report/render?xdo=/~weblogic/HAS/RPT_Building_Wise_House_Report.xdo&type=pdf&filename=building_wise_house_report')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">Buildingwise House Report</span>
						</a>
					</li>
					<li>
						<a target="_blank" href="{{url('/report/render?xdo=/~weblogic/HAS/RPT_HOUSE_ALLOTMENT.xdo&type=pdf&filename=house_allotment')}}">
							<i class="bx bx-right-arrow-alt"></i>
							<span class="menu-item">House Allotment</span>
						</a>
					</li>
				</ul>
			</li>

		</ul>


	</div>
</div>
<!-- END: Main Menu-->
<!-- END: Header-->
