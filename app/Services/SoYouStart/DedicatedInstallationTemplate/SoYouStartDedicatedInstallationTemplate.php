<?php
namespace App\Services\SoYouStart\DedicatedInstallationTemplate;

use Ovh\Api;

/**
 * SoYouStart API related to dedicated installation template
 *
 * @property PartitionScheme $partitionScheme
 */
class SoYouStartDedicatedInstallationTemplate
{
    protected $ovh_api;

    private static $mapStringToClass = [
        'partitionScheme' => PartitionScheme::class,
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
     * List all dedicated installation templates
     *
     * @return array An array of installation template names
     */
    public function all() {
        return $this->ovh_api->get('/dedicated/installationTemplate');
    }

    /**
     * Get dedicated installation template detail
     *
     * @param string $templateName Name of installation template
     * @return array Installation tempate details
     */
    public function get($templateName) {
        return $this->ovh_api->get(sprintf('/dedicated/installationTemplate/%s', $templateName));
    }

    /**
     * Get all dedicated installation template details
     *
     * @return array An array containing dedicated installation template information
     */
    public function templateInfos() {
        return $this->ovh_api->get('/dedicated/installationTemplate/templateInfos');
    }
}
