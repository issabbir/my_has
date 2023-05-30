<?php

namespace App\Http\Middleware;

use App\Entities\Security\Menu;
use Closure;
use Illuminate\Support\Facades\Auth;
use View;

class AclMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /*public function handle($request, Closure $next)
    {
        return $next($request);
    }*/

    public function handle($request, Closure $next)
    {
        $menus = [];

        if(Auth::check()) {
            if (auth()->user()->hasGrantAll()) {
                $menus = Menu::orderBy('menu_order_no')->get();
            } else {
                $menus = auth()->user()->getRoleMenus();
            }

            $pickedMenu[] = $menus[13]; // 14 is house allotment menu. In array index, 13 is house allotment menu object!

        }

        View::share('menus', $pickedMenu);

        return $next($request);
    }
}
