<?php

namespace App\Console\Commands;

use App\Services\PhpOvhNG\PhpOvhAsyncRequest;
use App\Services\PhpOvhNG\PhpOvhNG;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class TestAsyncDecodeCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:guzzlehttp:async';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[DEVELOPMENT USE ONLY] Simulate asynchronous GuzzleHTTP request and response';

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
        $start = microtime(true);
        /** @var PhpOvhNG $ovh_api*/
        $ovh_api = App::makeWith(PhpOvhNG::class, [
            'application_key' => config('soyoustart.application_key'),
            'application_secret' => config('soyoustart.application_secret'),
            'api_endpoint' => 'soyoustart-ca',
            'consumer_key' => config('soyoustart.consumer_key')
        ]);

        // Get all dedicated server and IPs available
        $phase1Promises = new PhpOvhAsyncRequest;
        $phase1Promises['dedicated.server'] = $ovh_api->get('/dedicated/server', null, null, true, true);
        $phase1Promises['ip'] = $ovh_api->get('/ip', null, null, true, true);

        $phase1Response = $ovh_api->callAsync($phase1Promises);
        $this->info(json_encode($phase1Response['dedicated.server']));
        $dedicatedServer = $phase1Response['dedicated.server'];
        $ips = $phase1Response['ip'];

        // Get all dedicated server IPs as well as list of available virtual MAC on per-server basis
        $phase2Promises = new PhpOvhAsyncRequest;
        foreach ($dedicatedServer as $serviceName) {
            $phase2Promises[$serviceName] = $ovh_api->get('/dedicated/server/' . $serviceName . '/ips', null, null, true, true);
            $phase2Promises[$serviceName . '.virtualMac'] = $ovh_api->get('/dedicated/server/' . $serviceName . '/virtualMac', null, null, true, true);
        }

        foreach ($ips as $ip) {
            $phase2Promises[$ip] = $ovh_api->get('/ip/' . urlencode($ip), null, null, true, true);
        }

        $phase2Responses = $ovh_api->callAsync($phase2Promises);

        $this->info(json_encode($phase2Responses));

        // Query all virtual MAC details as well, list of virtual address on per virtual MAC basis
        $phase3Promises = new PhpOvhAsyncRequest;
        $virtualMacs = [];
        foreach ($dedicatedServer as $serviceName) {
            // Grab every dedicated server available virtual MAC and start traversing through the MAC list
            $virtualMacs = $phase2Responses[$serviceName . '.virtualMac'];
            foreach ($virtualMacs as $virtualMac) {
                $phase3Promises[$serviceName . '.' . $virtualMac] = $ovh_api->get('/dedicated/server/'. $serviceName . '/virtualMac/' . $virtualMac, null, null, true, true);
                $phase3Promises[$serviceName . '.' . $virtualMac . '.virtualAddress'] = $ovh_api->get('/dedicated/server/'. $serviceName . '/virtualMac/' . $virtualMac . '/virtualAddress', null, null, true, true);
            }
        }

        // Query all available reverse domains on per IP block basis
        foreach ($ips as $ip) {
            $phase3Promises['ip.reverse.' . $ip] = $ovh_api->get('/ip/' . urlencode($ip) . '/reverse', null, null, true, true);
        }

        $phase3Responses = $ovh_api->callAsync($phase3Promises);
        $this->info(json_encode(array_keys($phase3Responses)));
        $phase4Promises = new PhpOvhAsyncRequest;
        $virtualMacArray = [];
        $virtualAddressArray = [];
        foreach ($dedicatedServer as $serviceName) {
            foreach (array_keys($phase3Responses) as $responseKey)
            {
                if (strpos($responseKey, $serviceName) === 0) {
                    if (strpos($responseKey, '.virtualAddress')) {
                        $virtualMacIps = $phase3Responses[$responseKey];
                        foreach ($virtualMacIps as $virtualMacIp) {
                            if (!$phase4Promises->offsetExists($virtualMacIp)) {
                                $macAddress = str_replace('.virtualAddress', '', str_replace($serviceName . '.', '', $responseKey));
                                $phase4Promises[$virtualMacIp] = $ovh_api->get('/dedicated/server/' . $serviceName . '/virtualMac/' . $macAddress . '/virtualAddress/' . urlencode($virtualMacIp), null, null, true, true);
                            }
                        }
                    } else {
                        $macAddress = str_replace($serviceName . '.', '', $responseKey);
                        $virtualMacArray[$macAddress] = $phase3Responses[$responseKey];
                    }
                }
            }
//            foreach ($virtualMacs as $virtualMac) {
//                $virtualAddresses = $phase3Responses[$serviceName . '.' . $virtualMac . '.virtualAddress'];
//                foreach ($virtualAddresses as $virtualAddress) {
//                    if (!$phase4Promises->offsetExists($serviceName. '.' . $virtualMac . '.virtualAddress' . $virtualAddress, $virtualAddresses)) {
//                        $phase4Promises[$serviceName. '.' . $virtualMac . '.virtualAddress' . $virtualAddress . '.' . $virtualAddresses] = $ovh_api->get('/dedicated/server/' . $serviceName . '/virtualMac', null, null, true, true);
//                    }
//                }
//            }
        }
        $phase4Responses = $ovh_api->callAsync($phase4Promises);
        $this->info(json_encode($phase4Responses));

//        $this->info(json_encode($phase3Responses));
//        $phase4Promises = new PhpOvhAsyncRequest;
//        foreach ($virt)
//
//
//        $phase4Responses = $ovh_api->callAsync($phase4Promises);
//        // Get virtual MAC available on per dedicated server basis
//
//        $virtualMacArray = [];
//        foreach ($dedicatedServer as $serviceName) {
//            foreach ($virtualMacs as $virtualMac) {
//                $virtualMacArray[$virtualMac] = $phase3Responses[$serviceName . '.' . $virtualMac];
//                $virtualMacArray[$virtualMac]['address'] = $phase3Responses[$serviceName . '.' . $virtualMac . '.virtualAddress'];
//            }
//        }
//
//        $rev
//        foreach ($ips as $ip) {
//
//        }
//
//        $phase5Promises = new PhpOvhAsyncRequest;
//        foreach ($virtualMacs as $virtualMac) {
////            $phase5Promises[$virtualMac] = $phaz
//        }
//        $this->info(json_encode($phase4Responses, JSON_PRETTY_PRINT));
//
////        $virtualMacArray = [];
////        foreach ($virtualMacs as $virtualMac) {
////            $virtualMacArray[$virtualMac] = [
////                'detail' => $phase4Responses[$virtualMac],
////                'reverse' => []
////            ];
////        }

        $end = microtime(true);
        $this->info($end - $start);
        return 0;
    }
}
