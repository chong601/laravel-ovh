<?php
namespace App\Services\SoYouStart\DedicatedServer;

use Ovh\Api;

class VirtualMac
{
    const VMAC_TYPE_ENUM = ["ovh","vmware"];
    protected $ovh_api;

    private static $mapStringToClass = [
        'virtualAddress' => VirtualAddress::class,
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

    public function all($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/virtualMac', $serviceName));
    }

    public function create($serviceName, $ipAddress, $type, $virtualMachineName) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/virtualMac', $serviceName), ['ipAddress' => $ipAddress, 'type' => $type, 'virtualMachineName' => $virtualMachineName]);
    }

    public function get($serviceName, $macAddress) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/virtualMac/%s', $serviceName, $macAddress));
    }
}
