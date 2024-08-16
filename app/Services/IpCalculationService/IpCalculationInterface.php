<?php

namespace App\Services\IpCalculationService;

use App\ViewModels\NetworkViewModel;

interface IpCalculationInterface
{
    public function SubnetInfoByIpv4(string $ip, string $prefix): NetworkViewModel;

    public function SubnetInfoByIpv6(string $ip, string $prefix): NetworkViewModel;
}