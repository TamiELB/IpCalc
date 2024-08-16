<?php

namespace app\IpCalculationService;

interface IpCalculationInterface
{
    public function SubnetInfoByIp4v($IP);

    public function SubnetInfoByIp6v($IP);
}