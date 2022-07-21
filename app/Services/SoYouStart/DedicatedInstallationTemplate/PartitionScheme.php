<?php
namespace App\Services\SoYouStart\DedicatedInstallationTemplate;

use Ovh\Api;

/**
 * SoYouStart API related to partition schemes
 *
 * @property HardwareRaid $hardwareRaid
 * @property Partition $partition
 */
class PartitionScheme {
    protected $ovh_api;

    private static $mapStringToClass = [
        'hardwareRaid' => HardwareRaid::class,
        'partition' => Partition::class
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

    /**
     * Get all partition schemes associated to this installation template
     *
     * @param string $templateName Template name
     * @return array List of partition schemes available for the installation template
     */
    public function all($templateName) {
        return $this->ovh_api->get(sprintf('/dedicated/installationTemplate/%s/partitionScheme', $templateName));
    }

    /**
     * Get partition scheme detail for the partition template
     *
     * @param string $templateName Template name
     * @param string $schemeName Partition scheme name
     * @return array Partition scheme details for the installation template
     */
    public function get($templateName, $schemeName) {
        return $this->ovh_api->get(sprintf('/dedicated/installationTemplate/%s/partitionScheme/%s', $templateName, $schemeName));
    }
}
