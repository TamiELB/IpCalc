<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IpCalculationService\IpCalculationInterface;
use App\ViewModels\NetworkViewModel;

class IpCalculationController extends Controller
{
    protected $IpCalculationService;

    public function __construct(IpCalculationInterface $IpCalculationService)
    {
        $this->ipCalculationService  = $IpCalculationService;
    }

    public function index(Request $request)
    {
        $requestString = $request->input('IP');

        $ip;
        $prefix;
        
        // Validate prefix
        if (strpos($requestString, '/') == false) 
            return response()->json(['error' => 'Prefix not provided'], 400);

        list($ip, $prefix) = explode('/', $requestString, 2);
            
        // Validate prefix length as an integer
        if (!is_numeric($prefix))
            return response()->json(['error' => 'Prefix is not numeric'], 400);

        $prefix = intval($prefix); // Parse to int
      
        $viewModel = new NetworkViewModel;

        // Validate Ip and Prefix for that Ip type
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false && ($prefix >= 1 && $prefix <= 32)) 
            $viewModel = $this->ipCalculationService->SubnetInfoByIpv4($ip, $prefix);
         else if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false && ($prefix >= 1 && $prefix <= 128)) 
            $viewModel = $this->ipCalculationService->SubnetInfoByIpv6($ip, $prefix);
        else 
            return response()->json(['error' => 'IP is invalid'], 400);
        
        return response()->json($viewModel);
    }
}
