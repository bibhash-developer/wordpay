<?php

namespace App\Http\Middleware;

use Closure;
//use Session;
class CheckAdminLoginMiddleware
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $action = web('request')->route()->getAction();
        $controller = class_basename($action['controller']);
        list($controller, $action) = explode('@', $controller);
        $new_action = compact('controller', 'action');
// pr($new_action);
// exit;


	//$userData = Session::get('user_data');
	if(!$request->session()->has('user_data')){
	    return redirect(url('/admin'));
	}
        return $next($request);
    }
}
