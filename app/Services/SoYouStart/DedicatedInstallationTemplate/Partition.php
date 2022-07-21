<?php
namespace App\Services\SoYouStart\DedicatedInstallationTemplate;

use Ovh\Api;

/**
 * SoYouStart API related to dedicated installation template
 */
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
        return $this->ovh_api->get(sprintf('/dedicated/installationTemplate/%s/partitionScheme/%s/partition', $templateName, $schemeName));
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
        return $this->ovh_api->get(sprintf('/dedicated/installationTemplate/%s/partitionScheme/%s/partition/%s', $templateName, $schemeName, urlencode($mountpoint)));
    }
}
