<?php
namespace App\Services\SoYouStart\DedicatedServer;

use Ovh\Api;

class Install
{
    protected $ovh_api;

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }

    public function compatibleTemplatePartitionSchemes($serviceName, $templateName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/install/compatibleTemplatePartitionSchemes', $serviceName), ['templateName' => $templateName]);
    }

    public function compatibleTemplates($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/install/compatibleTemplates', $serviceName));
    }

    public function hardwareRaidProfile($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/install/hardwareRaidProfile', $serviceName));
    }

    public function hardwareRaidSize($serviceName, $templateName, $partitionSchemeName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/install/hardwareRaidSize', $serviceName), ['templateName' => $templateName, 'partitionSchemeName' => $partitionSchemeName]);
    }

    public function start($serviceName, $templateName, $details = null, $partitionSchemeName = null, $userMetadata = null) {
        $params = [
            'templateName' => $templateName
        ];
        if (isset($details)) {
            $params['details'] = $details;
        }

        if (isset($partitionSchemeName)) {
            $params['partitionSchemeName'] = $partitionSchemeName;
        }

        if (isset($userMetadata)) {
            $params['userMetadata'] = $userMetadata;
        }

        return $this->ovh_api->post(sprintf('/dedicated/server/%s/install/start', $serviceName), $params);
    }

    public function status($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/install/status', $serviceName));
    }

    public function templateCapabilities($serviceName, $templateName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/install/templateCapabilities', $serviceName), ['templateName' => $templateName]);
    }
}
