<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use PhpIP\IPv4Block;

class SoYouStartListVirtualMacAndIpByServiceName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:listvirtualmacandipbyservicename {service_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List IPs and the virtual MAC assigned to the IP by the provided service name';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var \App\Services\SoYouStart\SoYouStartService */
        $ovh_api = App::makeWith(SoYouStartService::class, [
            'application_key' => config('soyoustart.application_key'),
            'application_secret' => config('soyoustart.application_secret'),
            'endpoint' => 'soyoustart-ca',
            'consumer_key' => config('soyoustart.consumer_key')
        ]);

        $serviceName = $this->argument('service_name');

        $ipMacMap = [];

        // Get all IPs assigned to the server
        $ipBlocks = $ovh_api->get(sprintf('/dedicated/server/%s/ips', $serviceName), []);

        // Get all virtual MACs for this server
        $virtualMacs = $ovh_api->get(sprintf('/dedicated/server/%s/virtualMac', $serviceName), []);
        // Sort it because OVH API never gives MAC addresses somewhat sorted (every run has different MAC arrangements)
        sort($virtualMacs);

        foreach ($ipBlocks as $ipBlock) {
            // Skip v6 because it's always too massive anyway :kek:
            // Virtual MACs doesn't apply on v6 addresses
            if (str_contains($ipBlock, ':')) {
                continue;
            }
            $ips = $this->ipBlockToIpArray($ipBlock);
            foreach ($ips as $ip) {
                $ipMacMap[$ip] = new \stdClass;
            }
        }
        // print_r($ipMacMap);
        // // Get all available virtual MAC assigned to the server
        $virtualMacs = $ovh_api->get(sprintf('/dedicated/server/%s/virtualMac', $serviceName), []);

        foreach ($virtualMacs as $virtualMac) {
            // Get the IP of this MAC address

            $virtualMacIps = $ovh_api->get(sprintf('/dedicated/server/%s/virtualMac/%s/virtualAddress', $serviceName, $virtualMac), []);
            foreach ($virtualMacIps as $virtualMacIp) {
                $ipAddressDetail = $ovh_api->get(sprintf('/dedicated/server/%s/virtualMac/%s/virtualAddress/%s', $serviceName, $virtualMac, $virtualMacIp), []);
                // print_r([$virtualMac, $virtualMacIp, $ipAddressDetail]);
            }
            $ipMacMap[$virtualMacIp] = ['mac' => $virtualMac, 'virtualMachineName' => $ipAddressDetail['virtualMachineName']];
        }
        print(json_encode($ipMacMap, JSON_PRETTY_PRINT));

        return 0;
    }

    /**
     * Convert a IP block to an array of IPs
     *
     * @return array
     */
    public function ipBlockToIpArray(string $ipBlock)
    {
        $block = IPv4Block::create($ipBlock);
        $ips = [];
        foreach ($block as $ip) {
            $ips[] = (string)$ip;
        }
        return $ips;
    }
}
