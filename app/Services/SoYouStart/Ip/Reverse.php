<?php
namespace App\Services\SoYouStart\Ip;

use Ovh\Api;

class Reverse
{
    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }
}
