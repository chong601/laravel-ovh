<?php

namespace App\Http\Controllers\WebApi\Dedicated\Server\VirtualMac;

use App\Http\Controllers\Controller;
use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class VirtualAddress extends Controller
{
    public function all($serviceName, $macAddress) {
        /** @var \App\Services\SoYouStart\SoYouStartService */
        $ovh_api = App::makeWith(SoYouStartService::class, [
            'application_key' => config('soyoustart.application_key'),
            'application_secret' => config('soyoustart.application_secret'),
            'endpoint' => 'soyoustart-ca',
            'consumer_key' => config('soyoustart.consumer_key')
        ]);

        return $ovh_api->dedicatedServer->virtualMac->virtualAddress->all($serviceName, $macAddress);
    }

    public function get($serviceName, $macAddress, $ipAddress) {
        /** @var \App\Services\SoYouStart\SoYouStartService */
        $ovh_api = App::makeWith(SoYouStartService::class, [
            'application_key' => config('soyoustart.application_key'),
            'application_secret' => config('soyoustart.application_secret'),
            'endpoint' => 'soyoustart-ca',
            'consumer_key' => config('soyoustart.consumer_key')
        ]);

        return $ovh_api->dedicatedServer->virtualMac->virtualAddress->get($serviceName, $macAddress, $ipAddress);
    }
}
