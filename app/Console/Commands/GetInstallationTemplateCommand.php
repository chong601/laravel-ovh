<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class GetInstallationTemplateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ovh:getinstallationtemplate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all available installation templates available';

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

        $result = $ovh_api->get('/dedicated/installationTemplate', '');

        print(json_encode($result, JSON_PRETTY_PRINT));
        return 0;
    }
}
