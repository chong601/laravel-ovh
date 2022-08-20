<?php

namespace App\Http\Controllers\WebApi\Ip;

use App\Http\Controllers\Controller;
use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Reverse extends Controller
{
    public function getIpReverse($serviceName) {
        $ovh_api = App::makeWith(SoYouStartService::class, [
            'application_key' => config('soyoustart.application_key'),
            'application_secret' => config('soyoustart.application_secret'),
            'endpoint' => 'soyoustart-ca',
            'consumer_key' => config('soyoustart.consumer_key')
        ]);

        $reverseList = [];
        $ipBlocks = $ovh_api->getDedicatedServerIpAddresses($serviceName);
        foreach ($ipBlocks as $ipBlock) {
            // Get the IPs with reverse DNS configured
            $ipsWithReverse = $ovh_api->getIpAddressReverseList($ipBlock);
            foreach ($ipsWithReverse as $ipWithReverse) {
                $reverseDetail = $ovh_api->getIpAddressReverseDetail($ipBlock, $ipWithReverse);
                // OVH API never send responses in sorted way, so we sort them
                ksort($reverseDetail);
                // unset ipReverse as we already put the IP as the JSON key
                unset($reverseDetail['ipReverse']);
                $reverseList[$ipWithReverse] = $reverseDetail;
            }
        }

        return $reverseList;
    }
}
