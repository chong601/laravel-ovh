<?php

namespace App\Console\Commands;

use App\Services\OvhCloud\OvhCloudService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class OvhCloudListAllOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ovh:listallorders {--start-date= : List order after the start date using ISO 8601 Date format (yyyy-mm-dd)} {--end-date= : List order before the end date using ISO 8601 Date format (yyyy-mm-dd)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all orders with optional start and end date\nStart date and end date are optional.';

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

        $args = $this->options();

        $arg_array = [];
        if ($args['start-date']) {
            $arg_array['date.from'] = $args['start-date'];
        }

        if ($args['end-date']) {
            $arg_array['date.to'] = $args['end-date'];
        }

        if (empty($arg_array)) {
            print(json_encode($ovh_api->get('/me/order'), JSON_PRETTY_PRINT));
        } else {
            print(json_encode($ovh_api->get('/me/order', $arg_array), JSON_PRETTY_PRINT));
        }

        return 0;
    }
}
