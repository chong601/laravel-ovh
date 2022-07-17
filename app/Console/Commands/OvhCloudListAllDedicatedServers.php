<?php

namespace App\Console\Commands;

use App\Services\OvhCloud\OvhCloudService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class OvhCloudListAllDedicatedServers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ovhcloud:dedicatedservers:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all subscribed OVHcloud dedicated servers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var \Ovh\Api */
        $ovh_api = App::makeWith(OvhCloudService::class, [
            'application_key' => config('ovh.application_key'),
            'application_secret' => config('ovh.application_secret'),
            'endpoint' => 'ovh-ca',
            'consumer_key' => config('ovh.consumer_key')
        ]);

        $results = $ovh_api->get('/dedicated/server');

        print(json_encode($results, JSON_PRETTY_PRINT));

        return 0;
    }
}
