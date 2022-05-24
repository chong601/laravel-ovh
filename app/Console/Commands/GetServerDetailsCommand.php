<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class GetServerDetailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ovh:getserverdetails {service_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get server details based on the service name provided';

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
        $ovh_api = App::makeWith(SoYouStartService::class, [
            'application_key' => config('ovh.application_key'),
            'application_secret' => config('ovh.application_secret'),
            'endpoint' => 'soyoustart-ca',
            'consumer_key' => config('ovh.consumer_key')
        ]);

        $serviceName = $this->argument('service_name');

        print(json_encode($ovh_api->get(sprintf('/dedicated/server/%s', $serviceName),''), JSON_PRETTY_PRINT));
        return 0;
    }
}
