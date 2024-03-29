<?php

namespace App\Http\Controllers\WebApi\Ip;

use App\Http\Controllers\Controller;
use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Reverse extends Controller
{
    public function all($ip) {
        /** @var \App\Services\SoYouStart\SoYouStartService */
        $ovh_api = App::makeWith(SoYouStartService::class, [
            'application_key' => config('soyoustart.application_key'),
            'application_secret' => config('soyoustart.application_secret'),
            'endpoint' => 'soyoustart-ca',
            'consumer_key' => config('soyoustart.consumer_key')
        ]);

        return $ovh_api->ip->reverse->all($ip);
    }

    public function get($ip, $ipReverse) {
        /** @var \App\Services\SoYouStart\SoYouStartService */
        $ovh_api = App::makeWith(SoYouStartService::class, [
            'application_key' => config('soyoustart.application_key'),
            'application_secret' => config('soyoustart.application_secret'),
            'endpoint' => 'soyoustart-ca',
            'consumer_key' => config('soyoustart.consumer_key')
        ]);

        return $ovh_api->ip->reverse->get($ip, $ipReverse);
    }
}
