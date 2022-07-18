<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartListAllReverseDnsEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:listallreversedns {service_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all reverse DNS entries of the server';

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

        // Get all IPs assigned to the server
        $ipBlocks = $ovh_api->getDedicatedServerIpAddresses($serviceName);
        foreach ($ipBlocks as $ipBlock) {
            // Get the IPs with reverse DNS configured
            $ipsWithReverse = $ovh_api->getIpAddressReverseList($ipBlock);
            foreach ($ipsWithReverse as $ipWithReverse) {
                $reverseDetail = $ovh_api->getIpAddressReverseDetail($ipBlock, $ipWithReverse);
                ksort($reverseDetail);
                $reverseList[strval($ipWithReverse)] = $reverseDetail;
            }
        }

        print(json_encode($reverseList, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return 0;
    }
}
