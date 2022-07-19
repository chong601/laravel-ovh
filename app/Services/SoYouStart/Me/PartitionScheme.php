<?php
namespace App\Services\SoYouStart\Me;

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
        return $this->ovh_api->get(sprintf('/me/installationTemplate/%s/partitionScheme', $templateName));
    }

    /**
     * Get partition scheme detail for the partition template
     *
     * @param string $templateName Template name
     * @param string $schemeName Partition scheme name
     * @return array Partition scheme details for the installation template
     */
    public function get($templateName, $schemeName) {
        return $this->ovh_api->get(sprintf('/me/installationTemplate/%s/partitionScheme/%s', $templateName, $schemeName));
    }

    /**
     * Create a new partition scheme for an installation template
     *
     * @param string $templateName Template name
     * @param string $schemeName Partition scheme name
     * @param int $priority The priority of the partition scheme
     * @return void
     */
    public function create($templateName, $schemeName, $priority) {
        $this->ovh_api->post(sprintf('/me/installationTemplate/%s/partitionScheme', $templateName), ['name' => $schemeName, 'priority' => $priority]);
    }

    /**
     * Delete a partition scheme from an installation template
     *
     * @param string $templateName Template name
     * @param string $schemeName Partition scheme name
     * @return void
     */
    public function delete($templateName, $schemeName) {
        $this->ovh_api->delete(sprintf('/me/installationTemplate/%s/partitionScheme/%s', $templateName, $schemeName));
    }

    /**
     * Update a partition scheme
     *
     * @param string $templateName Template name
     * @param string $schemeName Partition scheme name
     * @param string $updatedSchemeName An updated name for partition scheme name
     * @param int $updatedPriority The updated priority
     * @return void
     */
    public function update($templateName, $schemeName, $updatedSchemeName = null, $updatedPriority = null) {
        $parameterToUpdate = [];

        if(isset($updatedSchemeName)) {
            $parameterToUpdate['name'] = $updatedSchemeName;
        }
        if(isset($updatedPriority)) {
            $parameterToUpdate['priority'] = $updatedPriority;
        }

        if(empty($parameterToUpdate)) {
            // Nothing to update!
        }

        $this->ovh_api->put(sprintf('/me/installationTemplate/%s/partitionScheme/%s', $templateName, $schemeName), $parameterToUpdate);
    }
}
