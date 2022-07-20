<?php
namespace App\Services\SoYouStart\DedicatedServer;

use Ovh\Api;

class Boot
{
    protected $ovh_api;

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }
}
