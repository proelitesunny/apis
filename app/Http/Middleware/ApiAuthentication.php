<?php

namespace App\Http\Middleware;

use Closure;
use App\MyHealthcare\Auth\CryptAuthentication;
// use Illuminate\Support\Facades\Crypt;

class ApiAuthentication {

    private $cryptAuth;

    public function __construct(CryptAuthentication $cryptAuthentication) {
        $this->cryptAuth = $cryptAuthentication;
        //$secret_key = Crypt::encrypt("fortis-spine-dumper@2017.ndtvworldwide.com");
        //dd($secret_key);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $token = $request->header('apitoken');
        $aggregatorType = $request->header('aggregatorType');
        
        if (empty($aggregatorType)) {
            abort(400, trans('errors.AGGREGATOR_101'));
        }
        
        if (!in_array($aggregatorType, config('source.aggregator_types'))) {
            abort(400, trans('errors.AGGREGATOR_102'));
        }

        if (!empty($token) && !empty($aggregatorType)) {
            try {
                if(!$this->cryptAuth->validateAccessToken($token, $aggregatorType)) {
                    abort(400, trans('errors.PATIENT_117'));
                }
            } catch (\Exception $e) {
                abort(400, trans('errors.PATIENT_117'));
            }
        } else {
            abort(400, trans('errors.PATIENT_116'));
        }

        return $next($request);
    }

}
