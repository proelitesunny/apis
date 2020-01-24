<?php

namespace App\MyHealthcare\Helpers;

class GeoLocation
{
    private $lat;

    private $lng;

    private function setLat($output)
    {
        $this->lat = $output->results[0]->geometry->location->lat ? $output->results[0]->geometry->location->lat : null;
    }

    private function setLng($output)
    {
        $this->lng = $output->results[0]->geometry->location->lng ? $output->results[0]->geometry->location->lng : null;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function getLng()
    {
        return $this->lng;
    }

    public function getLatLng($address)
    {
        $geocode=file_get_contents(
            'https://maps.google.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false'
        );
        $output= json_decode($geocode);
        $this->setLat($output);
        $this->setLng($output);
        return $this;
    }
}
