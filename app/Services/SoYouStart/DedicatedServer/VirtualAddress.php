<?php
namespace App\Services\SoYouStart\DedicatedServer;

use Ovh\Api;

class VirtualAddress
{
    protected $ovh_api;

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }

    public function all($serviceName, $macAddress) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/virtualMac/%s/virtualAddress', $serviceName, $macAddress));
    }

    public function create($serviceName, $macAddress, $ipAddress, $virtualMachineName) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/virtualMac/%s/virtualAddress', $serviceName, $macAddress), ['ipAddress' => $ipAddress, 'virtualMachineName' => $virtualMachineName]);
    }

    public function delete($serviceName, $macAddress, $ipAddress) {
        return $this->ovh_api->delete(sprintf('/dedicated/server/%s/virtualMac/%s/virtualAddress/%s', $serviceName, $macAddress, urlencode($ipAddress)));
    }

    public function get($serviceName, $macAddress, $ipAddress) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/virtualMac/%s/virtualAddress/%s', $serviceName, $macAddress, urlencode($ipAddress)));
    }
}
