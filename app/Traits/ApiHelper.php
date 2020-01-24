<?php

namespace App\Traits;

trait ApiHelper
{
    protected function setOutputDateFormat($date)
    {
        return \DateTime::createFromFormat(config('api.aggregator_api.date_format.internal'), $date)->format(config('api.aggregator_api.date_format.output'));
    }

    protected function setOutputTimeFormat($date)
    {
        return \DateTime::createFromFormat(config('api.aggregator_api.time_format.internal'), $date)->format(config('api.aggregator_api.time_format.output'));
    }
    
    public function imageUrl($url)
    {
        $url = str_replace(' ', '%20', $url);
        $baseUrl = config('constants.upload_url') . $url;
        return $baseUrl;
    }
    
    public function getImageUrl()
    {
        $request = request();
        $baseUrl = \App::environment('production') ? config('constants.api_url') . 'images/' : config('api.image_url');
        return $baseUrl . $request->segment(3) . '/';
    }
}