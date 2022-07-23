<?php

namespace App\Console\Commands\SoYouStart\DedicatedServer\VirtualMac\VirtualAddress;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class AddVirtualAddressIP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:dedicatedserver:virtualmac:virtualaddress:add
                            {serviceName : Service name}
                            {macAddress : MAC address to add an IP}
                            {ipAddress : IP address to add to the MAC address}
                            {virtualMachineName : Virtual machine name associated to this IP/MAC combination}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add an IP to the MAC address';

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
        $virtualMachineName = $this->argument('virtualMachineName');

        $result = $ovh_api->dedicatedServer->virtualMac->virtualAddress->create($serviceName, $macAddress, $ipAddress, $virtualMachineName);

        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return 0;
    }
}
