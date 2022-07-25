<?php
namespace App\Services\OvhCloud\Order;

use Ovh\Api;

/**
 * A class that provice base functionality to interact with OVH API.
 *
 * It is recommended to extend the functionality outside of this service for specific use cases to keep the implementation clean.
 *
 * @property Catalog $catalog
 */
class OvhCloudOrder {

    /** @var \Ovh\Api */
    protected $ovh_api;

    private static $mapStringToClass = [
        'catalog' => Catalog::class
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
}
