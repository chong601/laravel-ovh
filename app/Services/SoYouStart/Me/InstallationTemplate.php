<?php
namespace App\Services\SoYouStart\Me;

use Ovh\Api;

/**
 * SoYouStart API related to installation templates
 *
 * @property PartitionScheme $partitionScheme
 */
class InstallationTemplate {

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
     * List all user-defined installation templates
     *
     * @return array An array of installation template names
     */
    public function all() {
        return $this->ovh_api->get('/dedicated/installationTemplate');
    }

    /**
     * Get user-defined installation template detail
     *
     * @param string $templateName Name of installation template
     * @return array Installation tempate details
     */
    public function get($templateName) {
        return $this->ovh_api->get(sprintf('/dedicated/installationTemplate/%s', $templateName));
    }

    /**
     * Create a user-defined installation template from an existing installation template
     *
     * @param string $templateName The name of the installation template
     * @param string $defaultLanguage The default language to set on the installation template
     * @param string $dedicatedTemplateName The dedicated template name to be based on
     * @return void
     */
    public function create($templateName, $defaultLanguage, $dedicatedTemplateName) {
        $this->ovh_api->post('/me/installationTemplate', ['name' => $templateName, 'defaultLanguage' => $defaultLanguage, 'baseTemplateName' => $dedicatedTemplateName]);
    }

    /**
     * Delete a user-defined installation template
     *
     * @param string $templateName The template name
     * @return void
     */
    public function delete($templateName) {
        $this->ovh_api->delete(sprintf('/me/installationTemplate/%s', $templateName));
    }

    /**
     * Update a user-defined installation template
     *
     * @param string $templatename The template name
     * @param array $templateData An array containing the data to update
     * @return void
     */
    public function update($templateName, $templateData) {
        $this->ovh_api->put(sprintf('/me/installationTemplate/%s', $templateName), $templateData);
    }

    /**
     * Checks the integrity of an installation template
     *
     * @return void
     */
    public function checkIntegrity($templateName) {
        $this->ovh_api->post(sprintf('/me/installationTemplate/%s/checkIntegrity', $templateName));
    }
}
