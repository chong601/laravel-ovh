<?php
namespace App\Services\SoYouStart\DedicatedServer;

use Ovh\Api;
use PhpIP\IPv4Block;
use PhpIP\IPv6Block;

/**
 * SoYouStart dedicated server
 *
 * @property Boot $boot
 * @property BringYourOwnImage $bringYourOwnImage
 * @property Features $features
 * @property Install $install
 * @property Intervention $intervention
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
 * @property VirtualNetworkInterface $virtualNetworkInterface
 */
class SoYouStartDedicatedServer
{
    const DATACENTER_ENUM = ["bhs1","bhs2","bhs3","bhs4","bhs5","bhs6","bhs7","bhs8","dc1","eri1","gra1","gra2","gra3","gsw","hil1","lim1","lim3","p19","rbx-hz","rbx1","rbx2","rbx3","rbx4","rbx5","rbx6","rbx7","rbx8","sbg1","sbg2","sbg3","sbg4","sbg5","sgp1","syd1","syd2","vin1","waw1"];
    const MRTG_PERIOD_ENUM = ["daily","hourly","monthly","weekly","yearly"];
    const MRTG_TYPE_ENUM = ["errors:download","errors:upload","packets:download","packets:upload","traffic:download","traffic:upload"];
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

    /**
     * Get all available dedicated servers
     *
     * @return array List of dedicated server service names
     */
    public function all() {
        return $this->ovh_api->get('/dedicated/server');
    }

    /**
     * Get all authentication secret available on a dedicated server
     *
     * @param string $serviceName Service name to fetch authentication secrets
     * @return array A list of authentication secrets available for the dedicated server
     */
    public function authenticationSecret($serviceName) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/authenticationSecret', $serviceName));
    }

    /**
     * Get details on offered backup cloud if available for the current server
     *
     * Note: Beta API endpoint
     *
     * @param $serviceName
     * @return mixed
     * @throws \JsonException
     */
    public function backupCloudOfferDetails($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/backupCloudOfferDetails', $serviceName));
    }

    /**
     * Confirm termination of a server
     *
     * @param string $serviceName Service name to terminate
     * @param string $token The token you have received from a termination request
     * @param string $commentary Optional comments on why you're terminating the service
     * @param string $futureUse Optional enum from self::TERMINATION_FUTURE_USE_ENUM to state your intention of what's next
     * @param string $reason Optional enum from self::TERMINATION_REASON_ENUM to state why you terminated this service
     * @return string ?????????????? SyS WHAT IS THE DESCRIPTION FFS
     */
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

    /**
     * Get service details for the dedicated server
     *
     * @param string $serviceName Service name to query
     * @param array Detailed information of the dedicated server
     */
    public function get($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s', $serviceName));
    }

    /**
     * Merge an IP block to the target dedicated server. This process is irreversible!
     *
     * @param string $serviceName Service name
     * @param string|IPv4Block|IPv6Block $ipBlock The IP block to merge into the dedicated server
     * @return array A task object on the IP block merge process
     */
    public function ipBlockMerge($serviceName, $block) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/ipBlockMerge', $serviceName), ['block' => strval($block)]);
    }

    /**
     * Check if the IP block can be moved to the dedicated server
     *
     * @param string $serviceName Service name
     * @param string|IPv4Block|IPv6Block $ip IP block to check
     * @return void
     */
    public function ipCanBeMovedTo($serviceName, $ip) {
        $this->ovh_api->get(sprintf('/dedicated/server/%s/ipCanBeMovedTo', $serviceName), ['ip' => urlencode(strval($ip))]);
    }

    /**
     * Get list of countries available for an IP order
     *
     * @param string $serviceName Service name
     * @return array A list of countries
     */
    public function ipCountryAvailable($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/ipCountryAvailable', $serviceName));
    }

    /**
     * Move an IP block to a dedicated server
     *
     * @param string $serviceName Service name
     * @param string string|IPv4Block|IPv6Block $ip IP block to transfer to this dedicated server
     * @return array A task object for this operation
     */
    public function ipMove($serviceName, $ip) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/ipMove', $serviceName), ['ip' => $ip]);
    }

    /**
     * List all IPs associated to this dedicated server
     *
     * @param string $serviceName Service name
     * @return array An array containing the IPs associated to this dedicated server
     */
    public function ips($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/ips', $serviceName));
    }

    /**
     * Get public logs for dedicated servers
     *
     * @param string $datacenter Fetch logs from specific datacenter
     * @param string $limit How many rows to fetch
     * @param string $page Page number to display
     * @param string $server Fetch logs by dedicated server
     * @return array An array of logs
     * @throws \JsonException
     */
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

    /**
     * Get traffic graph values
     *
     * @deprecated SyS has marked this API as deprecated
     * @param string $serviceName Service name
     * @param string $period Graph period
     * @param string $type Type of data to retrieve
     * @return array An array of graph values
     */
    public function mrtg($serviceName, $period, $type) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/mrtg', $serviceName), ['period' => $period, 'type' => $type]);
    }

    /**
     * Get a list of ongoing tasks for the server
     *
     * @param string $serviceName Service name
     * @return array An array of ongoing tasks on the specified dedicated server
     */
    public function ongoing($serviceName) {
        return $this->ovh_api->get(sprintf('/dedicated/server/%s/ongoing', $serviceName));
    }

    /**
     * Hard reboot the server
     *
     * Note: This reboot is similar to a hardware reset!
     *
     * @param string $serviceName Service name
     * @return array The task object for the reboot process
     */
    public function reboot($serviceName) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/reboot', $serviceName));
    }

    /**
     * Terminate dedicated server
     *
     * @param string $serviceName Service name
     * @return array
     */
    public function terminate($serviceName) {
        return $this->ovh_api->post(sprintf('/dedicated/server/%s/terminate', $serviceName));
    }
}
