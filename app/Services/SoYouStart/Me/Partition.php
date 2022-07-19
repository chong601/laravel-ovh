<?php
namespace App\Services\SoYouStart\Me;

use Ovh\Api;

class Partition {
    protected $ovh_api;

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }

    /**
     * List all partitions in the form of mount points available on this partition scheme
     *
     * @param string $templateName Template name
     * @param string $schemeName Partition scheme name
     * @return array A list of mount points defined
     */
    public function all($templateName, $schemeName) {
        return $this->ovh_api->get(sprintf('/me/installationTemplate/%s/partitionScheme/%s/partition', $templateName, $schemeName));
    }

    /**
     * Get partition details based on mount point defined
     *
     * @param string $templateName Template name
     * @param string $schemeName Partition scheme name
     * @param string $mountpoint
     * @return array Partition details
     */
    public function get($templateName, $schemeName, $mountpoint) {
        return $this->ovh_api->get(sprintf('/me/installationTemplate/%s/partitionScheme/%s/partition/%s', $templateName, $schemeName, urlencode($mountpoint)));
    }

    /**
     * Create a partition
     *
     * @param string $templateName Template name
     * @param string $schemeName Partition scheme name
     * @param string $filesystem File system to be created on this partition
     * @param string $mountpoint Mount point for this partition
     * @param string $raid RAID level to use on this partition
     * @param int $size Size in MiB
     * @param int $step Which position this partition will be located on the disk
     * @param string $type Type of the partition to be created
     * @param string $volumeName Volume name (applicable for type lv only)
     * @return void
     */
    public function create($templateName, $schemeName, $filesystem, $mountpoint, $raid, $size, $step, $type, $volumeName = null) {
        $this->ovh_api->post(sprintf('/me/installationTemplate/%s/partitionScheme/%s/partition', $templateName, $schemeName), [
            'filesystem' => $filesystem,
            'mountpoint' => $mountpoint,
            'raid' => $raid,
            'size' => $size,
            'step' => $step,
            'type' => $type,
            'volumeName' => $volumeName
        ]);
    }

    /**
     * Delete a partition
     *
     * @param string $templateName Template name
     * @param string $schemeName Partition scheme name
     * @param string $mountpoint Mount point
     * @return void
     */
    public function delete($templateName, $schemeName, $mountpoint) {
        $this->ovh_api->delete(sprintf('/me/installationTemplate/%s/partitionScheme/%s/partition/%s', $templateName, $schemeName, urlencode($mountpoint)));
    }

    /**
     * Update a partition
     *
     * @param string $templateName Template name
     * @param string $schemeName Partition scheme name
     * @param string $mountpoint Mount point
     * @param array $templatePartitions An array of partition data to update
     * @return void
     */
    public function update($templateName, $schemeName, $mountpoint, $templatePartitions) {
        $this->ovh_api->put(sprintf('/me/installationTemplate/%s/partitionScheme/%s/partition/%s', $templateName, $schemeName, urlencode($mountpoint)), $templatePartitions);
    }
}
