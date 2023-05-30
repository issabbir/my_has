<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand mt-0" href="{{route('dashboard')}}">
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
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation"
            data-icon-style="lines">
            <li class="nav-item sidebar-group-active">
                <a href="{{env('DASHBOARD_URL')}}"><i class="bx bx-home" data-icon="users"></i><span class="menu-item"
                                                                                                     data-i18n="Invoice List">Dashboard</span></a>
            </li>
            @php
                $activeMenus = \App\Helpers\HelperClass::activeMenus(\Illuminate\Support\Facades\Route::currentRouteName()) ;
                $hasActiveChildMenu = \App\Helpers\HelperClass::hasChildMenu(\Illuminate\Support\Facades\Route::currentRouteName());
            @endphp
            @foreach(\App\Helpers\HelperClass::menuSetup() as $menu)
                @if ($menu->module->enabled == 'Y')
                    <li class="nav-item {{  trim(Route::currentRouteName()) === trim($menu->menu_name) ? 'sidebar-group-active open ok' : 'none open' }}">
                        <a href=""><i class="bx bx-notepad" data-icon="users"></i><span class="menu-item"
                                                                                        data-i18n="Invoice List">{{$menu->menu_name}}</span></a>
                        <ul class="menu-content">
                            @foreach($menu->sub_menus as $submenu)
                                @if (Auth::user()->hasGrantAll() || in_array($submenu->submenu_id,$menu->role_submenus))
                                    <li class="{{($hasActiveChildMenu )?'open has-sub':((in_array($submenu->submenu_id,$activeMenus) && $submenu->route_name)?'active':'') }}">
                                        <a href="{{ (isset($submenu->action_name) && ($submenu->menu_id == \App\Enums\ModuleInfo::MODULE_ID))  ? route($submenu->action_name) : $submenu->route_name}}"
                                           @if ($submenu->route_name) class="link_item" @endif><i
                                                class="bx bx-right-arrow-alt"></i><span class="menu-item"
                                                                                        data-i18n="Third Level">{{$submenu->submenu_name}}</span></a>
                                        @if (count($submenu->submenus)>0)
                                            <ul class="menu-content">
                                                @foreach($submenu->submenus as $smenu)
                                                    @if (Auth::user()->hasGrantAll() || in_array($smenu->submenu_id,$menu->role_submenus))
                                                        <li class="{{in_array($smenu->submenu_id,$activeMenus)?'active':''}}">
                                                            @if (strpos($smenu->route_name, '.xdo') !== false)
                                                                <a href="{{ (isset($smenu->action_name) && ($smenu->menu_id == \App\Enums\ModuleInfo::MODULE_ID))  ? request()->root().route($smenu->action_name) : request()->root().$smenu->route_name}}"
                                                                   class="link_item" target="_blank"><i
                                                                        class="bx bx-right-arrow-alt"></i><span
                                                                        class="menu-item">{{$smenu->submenu_name}}</span></a>
                                                            @else
                                                                <a href="{{ (isset($smenu->action_name) && ($smenu->menu_id == \App\Enums\ModuleInfo::MODULE_ID))  ? route($smenu->action_name) : $smenu->route_name}}"
                                                                   class="link_item"
                                                                   @if (strpos($smenu->route_name, '.xdo') !== false) target="_blank" @endif><i
                                                                        class="bx bx-right-arrow-alt"></i><span
                                                                        class="menu-item">{{$smenu->submenu_name}}</span></a>
                                                            @endif
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
<!-- END: Header-->
