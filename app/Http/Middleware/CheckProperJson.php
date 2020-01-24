<?php

namespace App\Http\Middleware;

use Closure;

class CheckProperJson {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        
        if ($request->isJson() && ($request->isMethod('post'))) {
            if (empty($request->json()->all())) {
                abort(400, 'Not a valid json data');
            }
        }
        return $next($request);
    }

}
