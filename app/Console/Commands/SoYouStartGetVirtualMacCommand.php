<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartGetVirtualMacCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:getvirtualmac {service_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all virtual MAC addresses assigned to this server';

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

        print(json_encode($ovh_api->getDedicatedServerVirtualMacAddresses($serviceName), JSON_PRETTY_PRINT));
        return 0;
    }
}
