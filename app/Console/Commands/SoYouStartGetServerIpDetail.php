<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartGetServerIpDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:getserveripdetail {service_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get server IP details';

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

        // Get all IPs assigned to the server
        $ipBlocks = $ovh_api->get(sprintf('/dedicated/server/%s/ips', $serviceName), []);

        foreach ($ipBlocks as $ipBlock) {
            $ipBlockDetail = $ovh_api->get(sprintf('/ip/%s', urlencode(strval($ipBlock))), []);
            $ipBlockList[strval($ipBlock)] = $ipBlockDetail;
        }

        print(json_encode($ipBlockList, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return 0;
    }
}
