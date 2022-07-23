<?php

namespace App\Console\Commands\SoYouStart\DedicatedServer\VirtualMac\VirtualAddress;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class DeleteVirtualAddressIp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:dedicatedserver:virtualmac:virtualaddress:delete
                            {serviceName : Service name}
                            {macAddress : MAC address to list}
                            {ipAddress : IP address to query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove virtual IP from virtual MAC address';

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

        $serviceName = $this->argument('serviceName');
        $macAddress = $this->argument('macAddress');
        $ipAddress = $this->argument('ipAddress');

        $result = $ovh_api->dedicatedServer->virtualMac->virtualAddress->delete($serviceName, $macAddress, $ipAddress);

        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return 0;
    }
}
