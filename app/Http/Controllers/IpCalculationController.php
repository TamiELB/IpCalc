<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IpCalculationController extends Controller
{
    public function index(Request $request)
    {
        $string = $request->input('IP');
        
        if (filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false)
            return 2;
        else if (filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false)
            return true;
        else
            return false;
    }
}
