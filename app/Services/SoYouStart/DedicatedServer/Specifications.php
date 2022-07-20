<?php
namespace App\Services\SoYouStart\DedicatedServer;

use Ovh\Api;

class Specifications
{
    protected $ovh_api;

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }
}
