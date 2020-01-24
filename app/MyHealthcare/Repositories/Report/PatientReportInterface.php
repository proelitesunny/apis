<?php

namespace App\MyHealthcare\Repositories\Report;

interface PatientReportInterface
{
    public function getData($request);
    
    public function importReport($request);
    
    public function exportReport($request);
}