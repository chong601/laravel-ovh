<?php

namespace App\Console\Commands\SoYouStart\DedicatedServer\VirtualMac;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CreateVirtualMac extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:dedicatedserver:virtualmac:create
                            {serviceName : Service name}
                            {--type=ovh : Which MAC type to use}
                            {ipAddress : What IP address to assign to this MAC address}
                            {virtualMachineName : Virtual machine that will be associated to this MAC address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new virtual MAC and assign an IP to this virtual MAC address';

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
        $ipAddress = $this->argument('ipAddress');
        $virtualMachineName = $this->argument('virtualMachineName');

        $type = $this->option('type');

        $result = $ovh_api->dedicatedServer->virtualMac->create($serviceName, $ipAddress, $type, $virtualMachineName);

        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return 0;
    }
}
