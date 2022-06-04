<?php

namespace App\Console\Commands;

use App\Services\OvhCloud\OvhCloudService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class OvhCloudTrackOrderStatusBetaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beta:ovhcloud:trackorderstatus {order_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[BETA AF] Track OVHcloud order status';

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
        /** @var \Ovh\Api */
        $ovh_api = App::makeWith(OvhCloudService::class, [
            'application_key' => config('ovh.application_key'),
            'application_secret' => config('ovh.application_secret'),
            'endpoint' => 'ovh-ca',
            'consumer_key' => config('ovh.consumer_key')
        ]);

        $orderId = $this->argument('order_id');

        print(json_encode($ovh_api->get(sprintf('/me/order/%s/followUp', $orderId), []), JSON_PRETTY_PRINT));
        return 0;
    }
}
