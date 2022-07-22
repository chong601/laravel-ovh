<?php

namespace App\Console\Commands\SoYouStart\DedicatedServer\VirtualMac;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class ListAllVirtualMac extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:dedicatedserver:virtualmac:list {serviceName : Service name to query all virtual MACs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all virtual MAC configured on the dedicated server';

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

        $result = $ovh_api->dedicatedServer->virtualMac->all($serviceName);

        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
