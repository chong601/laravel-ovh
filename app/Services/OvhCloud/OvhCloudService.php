<?php
namespace App\Services\OvhCloud;

use Ovh\Api;

/**
 * A class that provice base functionality to interact with OVH API.
 *
 * It is recommended to extend the functionality outside of this service for specific use cases to keep the implementation clean.
 *
 * @property Auth $auth
 * @property Cdn $cdn
 * @property Cloud $cloud
 * @property Dbaas $dbaas
 * @property DedicatedAnthos $dedicatedAnthos
 * @property DedicatedCeph $dedicatedCeph
 * @property DedicatedHousing $dedicatedHousing
 * @property DedicatedInstallationTemplate $dedicatedInstallationTemplate
 * @property DedicatedNas $dedicatedNas
 * @property DedicatedNasHa $dedicatedNasHa
 * @property DedicatedServer $dedicatedServer
 * @property DedicatedCloud $dedicatedCloud
 * @property Deskaas $deskaas
 * @property Domain $domain
 * @property Email $email
 * @property HorizonView $horizonView
 * @property Hosting $hosting
 * @property Ip $ip
 * @property IpLoadBalancing $ipLoadBalancing
 * @property License $license
 * @property Me $me
 * @property MsServices $msServices
 * @property Order $order
 * @property OvhCloudConnect $ovhCloudConnect
 * @property Partner $partner
 * @property Price $price
 * @property Service $service
 * @property Services $services
 * @property Ssl $ssl
 * @property SslGateway $sslGateway
 * @property Startup $startup
 * @property Storage $storage
 * @property Support $support
 * @property Veeam $veeam
 * @property VeeamCloudConnect $veeamCloudConnect
 * @property Vip $vip
 * @property Vps $vps
 * @property Vrack $vrack
 */
class OvhCloudService {

    /** @var \Ovh\Api */
    protected $ovh_api;

    private static $mapStringToClass = [
        'auth' => Auth::class,
        'cdn' => Cdn::class,
        'cloud' => Cloud::class,
        'dbaas' => Dbaas::class,
        'dedicatedAnthos' => DedicatedAnthos::class,
        'dedicatedCeph' => DedicatedCeph::class,
        'dedicatedHousing' => DedicatedHousing::class,
        'dedicatedInstallationTemplate' => DedicatedInstallationtTemplate::class,
        'dedicatedNas' => DedicatedNas::class,
        'dedicatedNasHa' => DedicatedNasHa::class,
        'dedicatedCloud' => DedicatedCloud::class,
        'deskaas' => Deskaas::class,
        'domain' => Domain::class,
        'email' => Email::class,
        'horizonView' => HorizonView::class,
        'hosting' => Hosting::class,
        'ip' => Ip::class,
        'ipLoadBalancing' => IpLoadBalancing::class,
        'license' => License::class,
        'me' => Me::class,
        'msServices' => MsServices::class,
        'order' => Order::class,
        'ovhCloudConnect' => ovhCloudConnect::class,
        'partner' => Partner::class,
        'price' => Price::class,
        'service' => Service::class,
        'services' => Services::class,
        'ssl' => Ssl::class,
        'sslGateway' => SslGateway::class,
        'startup' => Startup::class,
        'storage' => Storage::class,
        'support' => Support::class,
        'veeam' => Veeam::class,
        'veeamCloudConnect' => VeeamCloudConnect::class,
        'vip' => Vip::class,
        'vps' => Vps::class,
        'vrack' => Vrack::class
    ];

    public function __construct(string $application_key, string $application_secret, string $endpoint, ?string $consumer_key)
    {
        $this->ovh_api = new Api($application_key, $application_secret, $endpoint, $consumer_key);
    }

    // Enable Stripe-like attribute accessing to a specific feature to query
    public function __get($name)
    {
        return array_key_exists($name, self::$mapStringToClass) ? new self::$mapStringToClass[$name]($this->ovh_api) : null;
    }

    /**
     * Performs a GET request to OVH API based on route and parameters
     */
    public function get(string $route, array $parameters = []) {
        // TODO: Exception handling
        // What is content?
        if ($parameters) {
            return $this->ovh_api->get($route, $parameters);
        }
        return $this->ovh_api->get($route);
    }

    /**
     * Performs a POST request to OVH API based on route and parameters
     */
    public function post(string $route, array $parameters = []) {
        // TODO: Exception handling
        // What is content?
        if ($parameters) {
            return $this->ovh_api->post($route, $parameters);
        }
        return $this->ovh_api->post($route);
    }

    /**
     * Performs a DELETE request to OVH API based on route and parameters
     */
    public function delete(string $route, array $parameters = []) {
        // TODO: Exception handling
        // What is content?
        if ($parameters) {
            return $this->ovh_api->delete($route, $parameters);
        }
        return $this->ovh_api->delete($route);
    }

    /**
     * Performs a PUT request to OVH API based on route and parameters
     */
    public function put(string $route, array $parameters = []) {
        // TODO: Exception handling
        // What is content?
        if ($parameters) {
            return $this->ovh_api->put($route, $parameters);
        }
        return $this->ovh_api->put($route, $parameters);
    }
}
