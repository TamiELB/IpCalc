<?php

namespace App\Services\IpCalculationService;

use App\ViewModels\NetworkViewModel;

class IpCalculationService implements IpCalculationInterface
{
    public function SubnetInfoByIpv4(string $ip, string $prefix): NetworkViewModel
    {
        $viewModel = new NetworkViewModel();

        $networkAddress = $this->calculateNetworkAddressIpv4($ip, $prefix);
        $firstAddress = $this->calculateFirstAddressIpv4($networkAddress);
        $lastAddress = $this->calculateLastAddressIpv4($networkAddress, $prefix);
        $totalHosts = $this->calculateTotalHostsIpv4($prefix);
        
        $viewModel->network = $networkAddress;
        $viewModel->first = $firstAddress;
        $viewModel->last = $lastAddress;
        $viewModel->hosts = $totalHosts;
        
        return $viewModel;
    }

    public function SubnetInfoByIpv6(string $ip, string $prefix): NetworkViewModel
    {
        $viewModel = new NetworkViewModel();

        $networkAddress = $this->calculateNetworkAddressIpv6($ip, $prefix);
        $firstAddress = $this->calculateFirstAddressIpv6($networkAddress);
        $lastAddress = $this->calculateLastAddressIpv6($networkAddress, $prefix);
        $totalHosts = $this->calculateTotalHostsIpv6($prefix);
        
        $viewModel->network = $networkAddress;
        $viewModel->first = $firstAddress;
        $viewModel->last = $lastAddress;
        $viewModel->hosts = $totalHosts;
        
        return $viewModel;
    }

    private function calculateNetworkAddressIpv4(string $ip, string $prefix): string
    {
        // Convert to binary integer example:
        // 192.168.1.10 == (3232235786 decimal) == 11000000 10101000 00000001 00001010 (binary)
        $ipBinaryString = ip2long($ip); 
        
        // -1 << for shifting bits to the right side
        // (32 - $prefix) == amount of times the bits will shift to the right
        // prefix of 3 (shifts 32 - 3 times) == 11111111 11111111 11111111 11111000
        $subnetMaskBinaryString = -1 << (32 - $prefix);

        // Bitwise (AND) operation
        // If the bit in the ip and subnet are both 1 that bit in the network will also be 1 otherwise 0
        // IP Address:         11000000 10101000 00000001 00000001 == 192.168.1.10
        // Subnet Mask:        11111111 11111111 11111111 00000000 == 255.255.255.0
        // Network:            11000000 10101000 00000001 00000000 == 192.168.1.0
        $networkAddressBinaryString = $ipBinaryString & $subnetMaskBinaryString;

        // Back to dotted ip
        return long2ip($networkAddressBinaryString);
    }

    private function calculateFirstAddressIpv4(string $networkAddress): string
    {
        // First addres is for the subnet it self
        return long2ip(ip2long($networkAddress) + 1);
    }

    private function calculateLastAddressIpv4(string $networkAddress, string $prefix): string
    {
        // Convert to binary integer example:
        // 192.168.1.10 == (3232235786 decimal) == 11000000 10101000 00000001 00001010 (binary)
        $networkBinaryString = ip2long($networkAddress);
        
        // Number of bits available for hosts
        $hostBits = 32 - $prefix;

        // Inverse Subnet and leave last address for (Broadcast)
        // 1 << for shifting bits to the left side 
        // $hostBits = amount of times it shifts
        // -1 to leave last address for broadcast
        $inverseSubnet = (1 << $hostBits) - 1;

        // Bitwise (OR) operation
        // If the bit in the ip and subnet are both 1 that bit in the network will also be 1 otherwise 0
        // IP Address:          11000000 10101000 00000001 00000001 == 192.168.1.10
        // Inverse Subnet Mask: 00000000 00000000 00000000 11111111 == 0.0.0.255
        // Network:             11000000 10101000 00000001 00000000 == 192.168.1.255 - 1 (broadcast address)
        $lastAddressBinaryString = ($networkBinaryString | $inverseSubnet) - 1;

        return long2ip($lastAddressBinaryString);
    }

    private function calculateTotalHostsIpv4(string $prefix): int
    {
        // Calculate available hosts by 2 raised to the power of prefix (available hosts)
        // (32 - prefix)^2
        // -2 available for network it self and broadcast
        return pow(2, 32 - intval($prefix)) - 2;
    }

    private function calculateNetworkAddressIpv6(string $ip, string $prefix): string
    {
        return $ip;
    }

    private function calculateFirstAddressIpv6(string $networkAddress): string
    {
        return $networkAddress; 
    }

    private function calculateLastAddressIpv6(string $networkAddress, string $prefix): string
    {
        // Implement IPv6 last address calculation
        return $networkAddress; // Placeholder, needs specific implementation
    }

    private function calculateTotalHostsIpv6(string $prefix): int
    {
        return pow(2, 128 - intval($prefix));
    }
}
