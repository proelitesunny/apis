<?php

namespace App\Http\Middleware;

use Closure;

class RunTimeConfig
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
        $dateconfig = [
            'bookings' => [
                'start_date' => \Carbon\Carbon::now(config('api.aggregator_api.timezone.user'))->format('Y-m-d H:i:s'),
                'end_date' => \Carbon\Carbon::now(config('api.aggregator_api.timezone.user'))->addMonth(1)->format('Y-m-d H:i:s'),
                'start_time' => \Carbon\Carbon::now(config('api.aggregator_api.timezone.user'))->startOfDay()->format('H:i:s'),
                'end_time' => \Carbon\Carbon::now(config('api.aggregator_api.timezone.user'))->addMonth(1)->endOfDay()->format('H:i:s'),
                'blocked_time' => \Carbon\Carbon::now('UTC')->subMinutes(15)->format('Y-m-d H:i:s'),
            ]
        ];

        config(['api.aggregator_api.bookings' => $dateconfig['bookings']]);

        return $next($request);
    }
}
