<div class="col-md-12 bg-white">
    <ol class="breadcrumb pb-0 mb-0 bg-rgba-secondary rounded-0">
        <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
        @if(\App\Helpers\HelperClass::breadCrumbs(\Illuminate\Support\Facades\Route::currentRouteName()))
            @foreach(\App\Helpers\HelperClass::breadCrumbs(\Illuminate\Support\Facades\Route::currentRouteName()) as $bm)
                {{--<li class="breadcrumb-item active mb-1">{{$bm['submenu_name']}}</li>--}}
                <li class="breadcrumb-item mb-1 {{!empty($bm['action_name'])?'active':''}}">
                    <a href="{{!empty($bm['action_name'])?(route($bm['action_name'])):'#'}}">{{$bm['submenu_name']}}</a>
                </li>
            @endforeach

        @endif
    </ol>
</div>
