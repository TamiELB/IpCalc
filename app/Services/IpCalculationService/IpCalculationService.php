<?php

namespace App\Services\IpCalculationService;

use App\ViewModels\NetworkViewModel;

class IpCalculationService implements IpCalculationInterface
{
    public function SubnetInfoByIpv4(string $ip, string $prefix): NetworkViewModel
    {
        $viewModel = new NetworkViewModel();

        $networkAddress = $this->CalculateNetworkAddressIpv4($ip, $prefix);
        $firstAddress = $this->CalculateFirstAddressIpv4($networkAddress);
        $lastAddress = $this->CalculateLastAddressIpv4($networkAddress, $prefix);
        $totalHosts = $this->CalculateUsableHostsIpv4($prefix);
        
        $viewModel->networkAddress = $networkAddress;
        $viewModel->firstAddress = $firstAddress;
        $viewModel->lastAddress = $lastAddress;
        $viewModel->usableHosts = $totalHosts;
        
        return $viewModel;
    }

    public function SubnetInfoByIpv6(string $ip, string $prefix): NetworkViewModel
    {
        $viewModel = new NetworkViewModel();

        // I am pretty sure Network addresses work a bit differnt than in ipv4 
        // because it uses a range of addresses, so I've set it the same as the "firstAddress"
        $networkAddress = $firstAddress = $this->calculateFirstAddressIpv6($ip, $prefix); 
        $lastAddress = $this->calculateLastAddressIpv6($networkAddress, $prefix);
        $totalHosts = $this->CalculateUsableHostsIpv6($prefix);
        
        $viewModel->networkAddress = $networkAddress;
        $viewModel->firstAddress = $firstAddress;
        $viewModel->lastAddress = $lastAddress;
        $viewModel->usableHosts = $totalHosts;
        
        return $viewModel;
    }

    private function CalculateNetworkAddressIpv4(string $ip, string $prefix): string
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

    private function CalculateFirstAddressIpv4(string $networkAddress): string
    {
        // First addres is for the subnet it self
        return long2ip(ip2long($networkAddress) + 1);
    }

    private function CalculateLastAddressIpv4(string $networkAddress, string $prefix): string
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

    private function CalculateUsableHostsIpv4(string $prefix): string
    {
        // Calculate available hosts by 2 raised to the power of prefix (available hosts)
        // 32 is the amount of bits a ipv6 can have
        // (32 - prefix)^2
        // -2 available for network it self and broadcast
        return max(pow(2, 32 - intval($prefix)) - 2, 0);
    }

    public function CalculateFirstAddressIpv6(string $ip, string $prefix): string
    {
        // Convert to 16byte binary string
        $ipBinaryString = inet_pton($ip);

        $subnetMaskBinaryString = $this->calculateMask($prefix);

        // Perform bitwise AND to get network address
        $networkAddressBinaryString = $this->binaryStringBitwiseAnd($ipBinaryString, $subnetMaskBinaryString);

        return inet_ntop($networkAddressBinaryString);
    }

    private function CalculateMask(string $prefix)
    {
        // Add full bytes of 0xff for the amount prefix is dividable by 8
        $mask = str_repeat("\xff", intdiv($prefix, 8));

        // Check for a partial byte (if prefix isn't fully dividable by 8)
        if ($prefix % 8 > 0) {
            $mask .= chr(0xff << (8 - ($prefix % 8))); // Shift remaining bits
        }
        $mask .= str_repeat("\x00", 16 - strlen($mask)); // calculate bytes and add remaining 00
    
        return $mask;
    }

    private function CalculateLastAddressIpv6(string $networkAddress, string $prefix): string
    {
        // Convert network address to binary string
        $networkBinaryString = inet_pton($networkAddress);

        $inverseSubnetMaskBinaryString = $this->calculateInverseMask($prefix);

        // Calculate the last address
        $lastAddressBinaryString = $this->binaryStringBitwiseOr($networkBinaryString, $inverseSubnetMaskBinaryString);
        
        // Convert binary string back to IPv6 address
        return inet_ntop($lastAddressBinaryString);
    }

    private function CalculateInverseMask(int $prefix): string
    {
        // Add empty bytes of 0x00 for the amount prefix is dividable by 8
        $inverseMask = str_repeat("\x00", intdiv($prefix, 8));

        // Check for a partial byte (if prefix isn't fully dividable by 8)
        if ($prefix % 8 > 0) {
            $inverseMask .= chr(0xff >> ($prefix % 8));
        }

        // Add full bytes 0xff untill it is 16 bytes long
        $inverseMask .= str_repeat("\xff", 16 - strlen($inverseMask));

        return $inverseMask;
    }

    private function BinaryStringBitwiseAnd(string $string1, string $string2): string
    {
        $result = '';

        // check in each byte if bits in both strings are set to 1 if not bit is set to 0
        // example
        // string1[0] = \xF0 = 240 == 11110000
        // string2[0] = \xAA = 170 == 10101010
        // $result    =   A0 = 160 == 10100000
        for ($i = 0; $i < strlen($string1); $i++) {
            $result .= chr(ord($string1[$i]) & ord($string2[$i]));
        }
        return $result;
    }


    private function BinaryStringBitwiseOr(string $a, string $b): string
    {
        $result = '';

        // check in each byte if a bit is set to 1 if so it returns a 1 for that bit
        // example
        // string1[0] = \xF0 = 240 == 11110000
        // string2[0] = \xAA = 170 == 10101010
        // $result    =   A0 = 160 == 11111010
        for ($i = 0; $i < strlen($a); $i++) {
            $result .= chr(ord($a[$i]) | ord($b[$i]));
        }

        return $result;
    }

    private function CalculateUsableHostsIpv6(string $prefix): string
    {
        // Calculate available hosts by 2 raised to the power of prefix (available hosts)
        // 128 is the amount of bits a ipv6 can have
        // (128 - prefix)^2
        return pow(2, 128 - intval($prefix));
    }

}

// ⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
// ⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣴⡾⢷⡄⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
// ⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣀⣠⣤⣴⠶⠶⠾⠋⠀⠘⠿⠶⣦⣄⡀⠀⠀⣀⣤⣄⡀⠀⠀⠀⠀⠀⠀⠀⠀
// ⠀⠀⣶⣤⣄⣀⣀⣤⡶⠟⠋⠉⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠉⠻⢶⣼⣿⡭⢍⢿⡆⠀⠀⠀⠀⠀⠀⠀
// ⠀⠀⣿⡄⠉⠙⠛⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣾⠇⠈⢿⠀⢸⡇⠀⠀⠀⠀⠀⠀⠀
// ⠀⠀⢸⣇⠀⠀⠀⢀⣤⣤⣤⡀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡀⠺⠗⠂⠛⠀⠀⠀⠀⣸⡇⠀⠀⠀⠀⠀⠀⠀
// ⠀⠀⢈⣿⢦⠀⣴⢿⣯⢷⠸⣧⢀⣶⡄⠀⠘⢷⠟⠛⠛⠁⠀⠀⢀⣀⣠⣤⣤⣶⡟⢀⣀⡀⠀⠀⠀⠀⠀
// ⠀⢀⡾⠃⠀⠒⠏⠨⣿⠶⢓⣿⠀⠉⠁⠀⣀⣀⣤⣴⡶⠶⣾⣛⣻⣽⣿⣶⡾⢾⣟⠛⠛⢿⡀⠀⠀⠀⠀
// ⢀⣿⠁⠀⠀⠀⢀⣀⣒⣴⣾⣷⣶⣶⣿⣿⣯⣿⠿⠾⠛⠛⣿⡋⠉⠀⢿⡄⠀⠀⢻⣆⠀⠈⢿⣦⡀⠀⠀
// ⠹⣿⡴⠶⠟⠛⠛⠋⢉⣿⡿⠿⠛⠻⣯⠁⠀⠻⣦⡀⠀⠀⠙⣷⡀⠀⠀⢻⣆⣀⣤⣿⠶⢶⣿⣿⣿⣦⡀
// ⠀⠀⠀⠀⠀⠀⠀⠀⢾⡏⢻⣆⠀⠀⠙⢷⣄⣀⣨⣷⣤⣤⡶⢾⣿⣻⣿⣿⣿⣿⡿⠾⠛⠛⠛⠉⠀⣿⡇
// ⠀⠀⠀⠀⠀⠀⠀⠀⠈⢷⣄⠹⣷⠟⣛⣻⣯⣭⣿⠾⠿⠿⠛⠛⠋⠉⠁⠀⢀⣀⣠⣤⣤⣶⠶⠶⠿⠻⠃
// ⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠻⣆⣿⡟⠉⠉⠀⠀⣀⢀⣀⣀⣀⣤⣤⡴⠶⠟⠛⠉⠉⠁⠀⠀⠀⠀⠀⠀⠀
// ⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠙⠿⠟⠛⠛⠛⠛⠛⠛⠛⠉⠉⠉⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀