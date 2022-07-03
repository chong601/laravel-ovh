<?php
namespace App\Services\SoYouStart;

use Ovh\Api;
use PhpIP\IPv4Block;
use PhpIP\IPv6Block;

/**
 * A class that provice base functionality to interact with OVH API.
 *
 * It is recommended to extend the functionality outside of this service for specific use cases to keep the implementation clean.
 */
class SoYouStartService {

    /** @var \Ovh\Api */
    protected $ovh_api;

    protected $dedicatedServer;

    protected $dedicatedInstallationTemplate;

    protected $ip;

    protected $me;

    public function __construct(string $application_key, string $application_secret, string $endpoint, ?string $consumer_key)
    {
        $this->ovh_api = new Api($application_key, $application_secret, $endpoint, $consumer_key);

        $this->dedicatedServer = new SoYouStartDedicatedServer($this->ovh_api);
        $this->dedicatedInstallationTemplate = new SoYouStartDedicatedInstallationTemplate($this->ovh_api);
        $this->ip = new SoYouStartIp($this->ovh_api);
        $this->me = new SoYouStartMe($this->ovh_api);
    }

    /**
     * Performs a GET request to OVH API based on route and parameters
     */
    public function get($route, $parameters) {
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
    public function post($route, $parameters) {
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
    public function delete($route, $parameters) {
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
    public function put($route, $parameters) {
        // TODO: Exception handling
        // What is content?
        if ($parameters) {
            return $this->ovh_api->put($route, $parameters);
        }
        return $this->ovh_api->put($route, $parameters);
    }

    /**
     * Get all available installation template that SoYouStart offers
     *
     * @return array An array of installation templates
     */
    public function getAllDedicatedInstallationTemplates() : array {
        return $this->ovh_api->get('/dedicated/installationTemplate');
    }

    /**
     * Get detailed information on the provided installation template name
     *
     * @param string $installationTemplate Installation template name to query the detail
     * @return array Installation template detail
     */
    public function getDedicatedInstallationTemplateDetail($installationTemplate) : array {
        return $this->ovh_api->get(sprintf('/dedicated/installationTemplate/%s', $installationTemplate));
    }

    /**
     * Get list of partitions defined on the provided installation template and partition scheme
     *
     * @param string $installationTemplate Installation template name
     * @param string $partitionScheme Partition scheme name
     * @return array List of mountpoints defined
     */
    public function getDedicatedInstallationTemplatePartitionSchemeMountpoints($installationTemplate, $partitionScheme) : array {
        return $this->ovh_api->get(sprintf('/dedicated/installationTemplate/%s/partitionScheme/%s/partition', $installationTemplate, $partitionScheme));
    }

    /**
     * Get mount point details on the provided installation template, partition scheme and mount point name
     *
     * @param string $installationTemplate Installation template name
     * @param string $partitionScheme Partition scheme name
     * @param string $mountpoint Mount point name
     * @return array Mount point details
     */
    public function getDedicatedInstallationTemplatePartitionSchemeMountpointDetails($installationTemplate, $partitionScheme, $mountpoint) : array {
        return $this->ovh_api->get(sprintf('/dedicated/installationTemplate/%s/partitionScheme/%s/partition/%s', $installationTemplate, $partitionScheme, urlencode($mountpoint)));
    }

    /**
     * Get all available dedicated servers related to the user
     *
     * @return array List of server names
     */
    public function getDedicatedServers() : array {
        return $this->ovh_api->get('/dedicated/server');
    }

    /**
     * Get all compatible installation templates for the dedicated server
     *
     * @param string $serviceName The service name
     * @return array List of compatible installation templates
     */
    public function getDedicatedServerCompatibleInstallationTemplates($serviceName) : array {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/install/compatibleTemplates', $serviceName));
    }

    /**
     * Get all partition scheme details of the installation template compatible for the server
     *
     * @param string $serviceName The service name of the dedicated server
     * @param string $installationTemplate The installation template name
     * @return array List of partition tables available for the installation template
     */
    public function getDedicatedServerCompatibleInstallationTemplatePartitionSchemes($serviceName, $installationTemplate) : array {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/install/compatibleTemplatePartitionSchemes', $serviceName), ['templateName' => $installationTemplate]);
    }

    /**
     * Get dedicated server details on the provided service name
     *
     * @param string $serviceName Service name of the dedicated server
     * @return array Dedicated server details
     */
    public function getDedicatedServerDetail($serviceName) : array {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s', $serviceName));
    }

    /**
     * Get dedicated server included IP addresses on the provided service name
     *
     * @param string $serviceName Service name of the dedicated server
     * @return array A list of IP addresses associated to the service name
     */
    public function getDedicatedServerIpAddresses($serviceName) : array {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/ips', $serviceName));
    }

    /**
     * Get dedicated server configured virtual MAC addresses
     *
     * @param string $serviceName Service name of the dedicated server
     * @return array A list of virtual MAC addresses associated to the service name
     */
    public function getDedicatedServerVirtualMacAddresses($serviceName) : array  {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/virtualMac', $serviceName));
    }

    /**
     * Get dedicated server configured virtual IP address assigned to virtual MAC address
     *
     * @param string $serviceName Service name of the dedicated server
     * @param string $virtualMac Virtual MAC address
     * @return array List of IP addresses configured to the virtual MAC address
     */
    public function getDedicatedServerVirtualMacIpAddresses($serviceName, $virtualMac) : array {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/virtualMac/%s/virtualAddress', $serviceName, $virtualMac));
    }

    /**
     * Get dedicated server virtual IP address detail assigned to virtual MAC address
     *
     * @param string $serviceName Service name of the dedicated server
     * @param string $virtualMac Virtual MAC address
     * @param string $virtualMacIp IP address attached to the virtual MAC
     * @return array Virtual IP details
     */
    public function getDedicatedServerVirtualMacIpAddressDetail($serviceName, $virtualMac, $virtualMacIp) : array {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/virtualMac/%s/virtualAddress/%s', $serviceName, $virtualMac, $virtualMacIp));
    }

    /**
     * Get IP block detail based on the provided IP
     *
     * @param string|IPv4Block|IPv6Block $ipBlock The IP address block to query
     * @return array IP block details
     */
    public function getIpBlockDetail($ipBlock) : array {
        return $this->ovh_api->get(sprintf('/ip/%s', urlencode(strval($ipBlock))));
    }

    /**
     * Get all user-defined templates
     *
     * @return array List of user-defined templates
     */
    public function getAllUserDefinedInstallationTemplates() : array{
        return $this->ovh_api->get('/me/installationTemplate');
    }

    /**
     * Get user-defined installation template details
     *
     * @param string $userInstallationTemplate Name of the installation template
     * @return array The installation template details
     */
    public function getUserDefinedInstallationTemplateDetails($userInstallationTemplate) : array {
        return $this->ovh_api->get(sprintf('/me/installationTemplate/%s', $userInstallationTemplate));
    }

    /**
     * Get user-defined installation template available partition schemes
     *
     * @param string $userInstallationTemplate Name of the installation template
     * @return array List of available partition schemes for the installation template
     */
    public function getUserDefinedInstallationTemplatePartitionSchemes($userInstallationTemplate) : array {
        return $this->ovh_api->get(sprintf('/me/installationTemplate/%s/partitionScheme', $userInstallationTemplate));
    }

    /**
     * Get user-defined installation template defined partition mount points in the partition scheme provided
     *
     * @param string $userInstallationTemplate Name of the installation template
     * @param string $partitionScheme Name of the partition scheme
     * @return array List of mount points defined in the installation partition scheme
     *
     */
    public function getUserDefinedInstallationTemplatePartitionMountpoints($userInstallationTemplate, $partitionScheme) : array {
        return $this->ovh_api->get(sprintf('/me/installationTemplate/%s/partitionScheme/%s/partition', $userInstallationTemplate, $partitionScheme));
    }

    /**
     * Get user-defined installation template defined partition mount point details in the partition scheme provided
     *
     * @param string $userInstallationTemplate Name of the installation template
     * @param string $partitionScheme Name of the partition scheme
     * @param string $mountpoint Mount point name
     * @return array The partition mount point details
     */
    public function getUserDefinedInstallationTemplatePartitionMountpointDetails($userInstallationTemplate, $partitionScheme, $mountpoint) : array {
        return $this->ovh_api->get(sprintf('/me/installationTemplate/%s/partitionScheme/%s/partition/%s', $userInstallationTemplate, $partitionScheme, urlencode($mountpoint)));
    }

    /**
     * Create a new user-defined template based on the provided dedicated installation template name
     *
     * @param string $userTemplateName Name of the new user-defined template name
     * @param string $dedicatedTemplateName Name of the dedicated installation template it will be based on
     * @return void
     */
    public function postCreateNewUserDefinedTemplate($userTemplateName, $dedicatedTemplateName) {
        $this->ovh_api->post('/me/installationTemplate', ['name' => $userTemplateName, 'defaultLanguage' => 'en', 'baseTemplateName' => $dedicatedTemplateName]);
    }

    /**
     * Create a new partition scheme for a user-defined template
     *
     * @param string $userTemplateName Name of the user-defined template
     * @param string $partitionSchemeName Name of the partition scheme
     * @param int $priority Priority of this partition (higher value = higher priority)
     * @return void
     */
    public function postCreateNewUserDefinedTemplatePartitionScheme($userTemplateName, $partitionSchemeName, $priority) {
        $this->ovh_api->post(sprintf('/me/installationTemplate/%s/partitionScheme', $userTemplateName), ['name' => $partitionSchemeName, 'priority' => $priority]);
    }

    /**
     * Update the partition scheme name or priority
     * Note: This function does not require both name and priority be passed in
     *
     * @param string $userTemplateName Name of the user-defined template
     * @param string $partitionSchemeName Name of the partition scheme name
     * @param string $newPartitionSchemeName New partition scheme name to update. Defaults to null.
     * @param string $newPriority New priority for the partition scheme name. Defaults to null
     * @return void
     */
    public function putUpdateUserDefinedTemplatePartitionScheme($userTemplateName, $partitionSchemeName, $newPartitionSchemeName = null, $newPriority = null) {
        if($newPartitionSchemeName) {
            $parameterToUpdate['name'] = $newPartitionSchemeName;
        }
        if($newPriority) {
            $parameterToUpdate['priority'] = $newPriority;
        }

        $this->ovh_api->put(sprintf('/me/installationTemplate/%s/partitionScheme/%s', $userTemplateName, $partitionSchemeName), $parameterToUpdate);
    }
}
