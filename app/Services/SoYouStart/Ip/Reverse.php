<?php
namespace App\Services\SoYouStart\Ip;

use Ovh\Api;
use PhpIP\IPv4;
use PhpIP\IPv4Block;
use PhpIP\IPv6;
use PhpIP\IPv6Block;

class Reverse
{
    protected $ovh_api;

    public function __construct(Api $ovh_api)
    {
        $this->ovh_api = $ovh_api;
    }

    /**
     * List all IPs with reverse domain configured
     *
     * @param string|IPv4Block|IPv6Block $ip IP block
     * @return array An array of IPs with reverse domain configured
     */
    public function all($ip) {
        return $this->ovh_api->get(sprintf('/ip/%s/reverse', urlencode($ip)));
    }

    /**
     * Create a reverse domain to an IP that is a part of an IP block
     *
     * @param string|IPv4Block|IPv6Block $ip IP block
     * @param string|IPv4|IPv6 $ipReverse IP address to set reverse domain name
     * @param string $reverse The reverse domain name to set
     * @return array The reverse domain object
     */
    public function create($ip, $ipReverse, $reverse) {
        return $this->ovh_api->post(sprintf('/ip/%s/reverse', urlencode($ip)), [
            'ipReverse' => urlencode($ipReverse),
            'reverse' => $reverse
        ]);
    }

    /**
     * Get IP reverse data attached to an IP from an IP block
     *
     * @param string|IPv4Block|IPv6Block $ip IP block
     * @param string|IPv4|IPv6 $ipReverse IP address to query reverse data
     * @return array The reverse domain object
     */
    public function get($ip, $ipReverse) {
        return $this->ovh_api->get(sprintf('/ip/%s/reverse/%s', urlencode($ip), urlencode($ipReverse)));
    }

    /**
     * Delete reverse domain from an IP
     *
     * @param string|IPv4Block|IPv6Block $ip IP block
     * @param string|IPv4|IPv6 $ipReverse IP address to delete reverse domain
     * @return void
     */
    public function delete($ip, $ipReverse) {
       $this->ovh_api->delete(sprintf('/ip/%s/reverse/%s', $ip, $ipReverse));
    }
}
