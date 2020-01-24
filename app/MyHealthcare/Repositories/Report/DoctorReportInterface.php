<?php

namespace App\MyHealthcare\Repositories\Report;

interface DoctorReportInterface
{
    public function getData($request);
    
    public function importReport($request);
    
    public function exportReport($request);
}