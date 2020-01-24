<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DocumentationController extends Controller
{

    public function aggregatorDocsV1()
    {
        return view('admin.docs.aggregator.v1');
    }
    
}