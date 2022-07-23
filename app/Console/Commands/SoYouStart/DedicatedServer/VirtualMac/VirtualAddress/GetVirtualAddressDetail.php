<?php

namespace App\Console\Commands\SoYouStart\DedicatedServer\VirtualMac\VirtualAddress;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class GetVirtualAddressDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:dedicatedserver:virtualmac:virtualaddress:get
                            {serviceName : Service name}
                            {macAddress : MAC address to list}
                            {ipAddress : IP address to query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get virtual IP information';

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

        $result = $ovh_api->dedicatedServer->virtualMac->virtualAddress->get($serviceName, $macAddress, $ipAddress);

        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return 0;
    }
}
