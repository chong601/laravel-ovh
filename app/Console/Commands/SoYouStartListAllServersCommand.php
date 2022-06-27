<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Support\Facades\App;

class SoYouStartListAllServersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:listallservers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists all available servers';

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
            'application_key' => config('soyoustart.application_key'),
            'application_secret' => config('soyoustart.application_secret'),
            'endpoint' => 'soyoustart-ca',
            'consumer_key' => config('soyoustart.consumer_key')
        ]);

        print(json_encode($ovh_api->get('/dedicated/server','')));
        return 0;
    }
}
