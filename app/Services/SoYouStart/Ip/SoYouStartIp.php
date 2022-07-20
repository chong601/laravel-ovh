<?php
namespace App\Services\SoYouStart\Ip;

use Ovh\Api;
use PhpIP\IPv4Block;
use PhpIP\IPv6Block;

/**
 * SoYouStart IP related operations
 *
 * @property AntiHack $antihack
 * @property Arp $arp
 * @property Game $game
 * @property License $license
 * @property MigrationToken $migrationToken
 * @property Move $move
 * @property Park $park
 * @property Phishing $phishing
 * @property Reverse $reverse
 * @property Ripe $ripe
 * @property Service $service
 * @property Spam $spam
 * @property Task $task
 */
class SoYouStartIp
{
    protected $ovh_api;

    const IP_TYPE_ENUM = ["cdn", "cloud", "dedicated", "failover", "hosted_ssl", "housing", "loadBalancing", "mail", "overthebox", "pcc", "pci", "private", "vpn", "vps", "vrack", "xdsl"];

    private static $mapStringToClass = [
        'antihack' => AntiHack::class,
        'arp' => Arp::class,
        'game' => Game::class,
        'license' => License::class,
        'migrationToken' => MigrationToken::class,
        'move' => Move::class,
        'park' => Park::class,
        'phishing' => Phishing::class,
        'reverse' => Reverse::class,
        'ripe' => Ripe::class,
        'service' => Service::class,
        'spam' => Spam::class,
        'task' => Task::class
    ];

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }

    // Thanks Stripe for this design!
    public function __get($name)
    {
        return array_key_exists($name, self::$mapStringToClass) ? new self::$mapStringToClass[$name]($this->ovh_api) : null;
    }

    /**
     * Get all IP available on this user
     *
     * @param string $description Search by IP description. Optional.
     * @param string|IPv4Block|IPv6Block $ip Search by IP address/block. Optional.
     * @param string $routedToServiceName Search by service name. Optional.
     * @param string $type Search by IP type. Optional.
     * @return array A list of IPs the user has
     */
    public function all($description = null, $ip = null, $routedToServiceName = null, $type = null) {
        $query = [];

        if (isset($description)) {
            $query['description'] = $description;
        }

        if (isset($ip)) {
            $query['ip'] = $ip;
        }

        if (isset($routedToServiceName)) {
            $query['routedTo.serviceName'] = $routedToServiceName;
        }

        if (isset($type) && in_array($type, self::IP_TYPE_ENUM)) {
            $query['type'] = $type;
        }

        if ($query) {
            return $this->ovh_api->get('/ip', $query);
        }

        return $this->ovh_api->get('/ip');
    }

    /**
     * Get IP address/block details
     *
     * @param string|IPv4Block|IPv6Block $ip IP address/block to query
     * @return array IP address/block details
     */
    public function get($ip) {
        return $this->ovh_api->get(sprintf('/ip/%s', urlencode($ip)));
    }

    /**
     * Update the description of the IP address/block
     *
     * @param string|IPv4Block|IPv6Block $ip IP address/block to set description
     * @param string $description Description to set on the IP address/block
     * @return void
     */
    public function update($ip, $description) {
        $this->ovh_api->get(sprintf('/ip/%s', urlencode($ip)), ['description' => $description]);
    }

    /**
     * Change the RIPE organization ID of the IP address/block
     *
     * @param string|IPv4Block|IPv6Block $ip IP address to change RIPE organization ID
     * @param string $ripeOrg The RIPE organization ID to change to
     * @return array The task object created to track the status
     */
    public function changeOrg($ip, $ripeOrg) {
        return $this->ovh_api->post(sprintf('/ip/%s/changeOrg', urlencode($ip)), ['organisation' => $ripeOrg]);
    }

    /**
     * Terminate the provided IP address/block
     *
     * @deprecated Marked as deprecated on SoYouStart API Console
     * @see Service The service class that will contain the terminate function
     * @param string|IPv4Block|IPv6Block IP address/block to terminate
     * @return array The task object created to track the status
     */
    public function terminate($ip) {
        return $this->ovh_api->post(sprintf('/ip/%s/terminate', urlencode($ip)));
    }
}
