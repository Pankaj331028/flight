<?php

namespace App\Http\Middleware;

use App\Model\Notification;
use Auth;
use Closure;

class shareMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->user()) {

            $notifications = Notification::where('is_read', '0')->where('receiver_id', Auth::guard('admin')->user()->id)->get();
            \View::share('notifications', $notifications);
        }
        // dd($next, $request->url());
        return $next($request);
    }

}
