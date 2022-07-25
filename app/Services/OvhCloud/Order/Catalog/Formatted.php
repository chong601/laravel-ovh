<?php
namespace App\Services\OvhCloud\Order\Catalog;

use Ovh\Api;

/**
 * Provides formatted list of available offerings for OVH services
 *
 */
class Formatted {

    /** @var \Ovh\Api */
    protected $ovh_api;

    public function __construct(string $application_key, string $application_secret, string $endpoint, ?string $consumer_key)
    {
        $this->ovh_api = new Api($application_key, $application_secret, $endpoint, $consumer_key);
    }

    public function bringYourOwnIp($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function cloud($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function dedicated($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function deskaas($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function discover($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function ip($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function licenseCloudLinux($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function licensecPanel($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function licenseDirectadmin($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function licensePlesk($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function licenseSqlServer($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function licenseVirtuozzo($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function licenseWindows($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function licenseWorklight($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function logs($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function privateCloud($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function privateCloudCDI($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function privateCloudDC($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function privateCloudEnterprise($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function privateCloudReseller($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function privateCloudResellerEnterprise($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function reseller($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }

    public function vps($ovhSubsidiary) {
        return $this->ovh_api->get('/order/catalog/formatted/bringYourOwnIp', ['ovhSubsidiary' => $ovhSubsidiary]);
    }
}
