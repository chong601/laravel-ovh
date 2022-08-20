<?php

namespace App\Http\Controllers\WebApi\Ip;

use App\Http\Controllers\Controller;
use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use PhpIP\IPv4Block;

class VirtualMac extends Controller
{
    public function getVirtualMac($serviceName) {
        $ovh_api = App::makeWith(SoYouStartService::class, [
            'application_key' => config('soyoustart.application_key'),
            'application_secret' => config('soyoustart.application_secret'),
            'endpoint' => 'soyoustart-ca',
            'consumer_key' => config('soyoustart.consumer_key')
        ]);

        $ipMacMap = [];
        // Get all IPs assigned to the server
        $ipBlocks = $ovh_api->getDedicatedServerIpAddresses($serviceName);

        foreach ($ipBlocks as $ipBlock) {
            // Skip v6 because it's always too massive anyway :kek:
            // Virtual MACs doesn't apply on v6 addresses
            if (str_contains($ipBlock, ':')) {
                continue;
            }
            $block = IPv4Block::create($ipBlock);
            $ips = [];
            foreach ($block as $ip) {
                $ips[] = strval($ip);
            }

            foreach ($ips as $ip) {
                $ipMacMap[$ip] = new \stdClass;
            }
        }

        // Get all available virtual MAC assigned to the server
        $virtualMacs = $ovh_api->getDedicatedServerVirtualMacAddresses($serviceName);
        sort($virtualMacs);

        foreach ($virtualMacs as $virtualMac) {
            // Get the IP of this MAC address
            $virtualMacIps = $ovh_api->getDedicatedServerVirtualMacIpAddresses($serviceName, $virtualMac);
            foreach ($virtualMacIps as $virtualMacIp) {
                $ipAddressDetail = $ovh_api->getDedicatedServerVirtualMacIpAddressDetail($serviceName, $virtualMac, $virtualMacIp);
            }
            $ipMacMap[$virtualMacIp] = ['mac' => $virtualMac, 'virtualMachineName' => $ipAddressDetail['virtualMachineName']];
        }

        return $ipMacMap;
    }
}
