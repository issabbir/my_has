<?php

namespace App\Http\Middleware;

use Closure;

// App Authorization for CPA
/**
 * Class Authorize
 * @package App\Http\Middleware
 */
class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next , ...$perms)
    {
        $goNext = false;

        if($perms) {
            foreach($perms as $perm) {
                if($permission = auth()->user()->hasPermission($perm)) {
                    $goNext = $goNext || $permission;
                }
            }
        }

        if(!$goNext) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
