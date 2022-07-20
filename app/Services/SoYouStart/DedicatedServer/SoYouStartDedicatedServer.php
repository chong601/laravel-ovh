<?php
namespace App\Services\SoYouStart;

use Ovh\Api;

class SoYouStartDedicatedServer
{
    protected $ovh_api;

    private static $mapStringToClass = [
        'authenticationSecret' => AuthenticationSecret::class,
        'boot' => Boot::class,
        'bringYourOwnImage' => BringYourOwnImage::class,
        'features' => Features::class,
        'install' => Install::class,
        'intervention' => Intervention::class,
        'license' => License::class,
        'networkInterfaceController' => NetworkInterfaceController::class,
        'option' => Option::class,
        'orderable' => Orderable::class,
        'secondaryDnsDomains' => SecondaryDnsDomains::class,
        'serviceMonitoring' => ServiceMonitoring::class,
        'specifications' => Specifications::class,
        'spla' => Spla::class,
        'statistics' => Statistics::class,
        'support' => Support::class,
        'task' => Task::class,
        'virtualMac' => VirtualMac::class,
        'virtualNetworkInterface' => VirtualNetworkInterface::class
    ];

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }

    // Enable Stripe-like attribute accessing to a specific feature to query
    public function __get($name)
    {
        return array_key_exists($name, self::$mapStringToClass) ? new self::$mapStringToClass[$name]($this->ovh_api) : null;
    }
}
