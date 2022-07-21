<?php
namespace App\Services\SoYouStart\DedicatedServer;

use Ovh\Api;

/**
 * SoYouStart dedicated server
 *
 * @property Boot $boot
 * @property BringYourOwnImage $bringYourOwnImage
 * @property Features $features
 * @property Install $install
 * @property Intervention $inttervention
 * @property License $license
 * @property NetworkInterfaceController $networkInterfaceController
 * @property Option $option
 * @property SecondaryDnsDomains $secondaryDnsDomains
 * @property ServiceInfos $serviceInfos
 * @property ServiceMonitoring $serviceMonitoring
 * @property Specifications $specifications
 * @property Spla $spla
 * @property Statistics $statistics
 * @property Support $support
 * @property Task $task
 * @property VirtualMac $virtualMac
 * @property VirtualNetworkInterface $virtialNetworkInterface
 */
class SoYouStartDedicatedServer
{
    const DATACENTER_ENUM = ["bhs1","bhs2","bhs3","bhs4","bhs5","bhs6","bhs7","bhs8","dc1","eri1","gra1","gra2","gra3","gsw","hil1","lim1","lim3","p19","rbx-hz","rbx1","rbx2","rbx3","rbx4","rbx5","rbx6","rbx7","rbx8","sbg1","sbg2","sbg3","sbg4","sbg5","sgp1","syd1","syd2","vin1","waw1"];
    const TERMINATION_FUTURE_USE_ENUM = ["NOT_REPLACING_SERVICE","OTHER","SUBSCRIBE_AN_OTHER_SERVICE","SUBSCRIBE_OTHER_KIND_OF_SERVICE_WITH_COMPETITOR","SUBSCRIBE_SIMILAR_SERVICE_WITH_COMPETITOR"];
    const TERMINATION_REASON_ENUM = ["FEATURES_DONT_SUIT_ME","LACK_OF_PERFORMANCES","MIGRATED_TO_ANOTHER_OVH_PRODUCT","MIGRATED_TO_COMPETITOR","NOT_ENOUGH_RECOGNITION","NOT_NEEDED_ANYMORE","NOT_RELIABLE","NO_ANSWER","OTHER","PRODUCT_DIMENSION_DONT_SUIT_ME","PRODUCT_TOOLS_DONT_SUIT_ME","TOO_EXPENSIVE","TOO_HARD_TO_USE","UNSATIFIED_BY_CUSTOMER_SUPPORT"];

    protected $ovh_api;

    private static $mapStringToClass = [
        'boot' => Boot::class,
        'bringYourOwnImage' => BringYourOwnImage::class,
        'features' => Features::class,
        'install' => Install::class,
        'intervention' => Intervention::class,
        'license' => License::class,
        'networkInterfaceController' => NetworkInterfaceController::class,
        'option' => Option::class,
        'orderable' => Orderable::class,
        'secondaryDnsDomains' => SecondaryDnsDomains::class,
        'serviceInfos' => ServiceInfos::class,
        'serviceMonitoring' => ServiceMonitoring::class,
        'specifications' => Specifications::class,
        'spla' => Spla::class,
        'statistics' => Statistics::class,
        'support' => Support::class,
        'task' => Task::class,
        'virtualMac' => VirtualMac::class,
        'virtualNetworkInterface' => VirtualNetworkInterface::class
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

    public function all() {
        return $this->ovh_api->get('/dedicated/server');
    }

    public function authenticationSecret($serviceName) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/authenticationSecret', $serviceName));
    }

    public function backupCloudOfferDetails($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/backupCloudOfferDetails', $serviceName));
    }

    public function confirmTermination($serviceName, $token, $commentary = null, $futureUse = null, $reason = null) {
        $params = ['token' => $token];

        if (isset($commentary)) {
            $params['commentary'] = $commentary;
        }

        if (in_array($futureUse, self::TERMINATION_FUTURE_USE_ENUM)) {
            $params['futureUse'] = $futureUse;
        }

        if (in_array($reason, self::TERMINATION_REASON_ENUM)) {
            $params['reason'] = $reason;
        }

        return $this->ovh_api->post(sprintf('/dedicated/server/%s/confirmTermination', $serviceName), $params);
    }

    public function get($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s', $serviceName));
    }

    public function ipBlockMerge($serviceName, $block) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/ipBlockMerge', $serviceName), ['block' => $block]);
    }

    public function ipCanBeMovedTo($serviceName, $ip) {
        $this->ovh_api->get(sprintf('/dedicated/server/$s/ipCanBeMovedTo', $serviceName), ['ip' => $ip]);
    }

    public function ipCountryAvailable($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/ipCountryAvailable', $serviceName));
    }

    public function ipMove($serviceName, $ip) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/ipMove', $serviceName), ['ip' => $ip]);
    }

    public function ips($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/ips', $serviceName));
    }

    public function mrtg($serviceName, $period, $type) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/mrtg', $serviceName), ['period' => $period, 'type' => $type]);
    }

    public function ongoing($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/ongoing', $serviceName));
    }

    public function reboot($serviceName) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/reboot', $serviceName));
    }

    public function log($datacenter = null, $limit = null, $page = null, $server = null) {
        $params = [];

        if (in_array($datacenter, self::DATACENTER_ENUM)) {
            $params['datacenter'] = $datacenter;
        }

        if (isset($limit)) {
            $params['limit'] = $limit;
        }

        if (isset($page)) {
            $params['page'] = $page;
        }

        if (isset($server)) {
            $params['server'] = $server;
        }

        if ($params) {
            return $this->ovh_api->get('/dedicated/server/log', $params);
        }
        return $this->ovh_api->get('/dedicated/server/log');
    }
}
