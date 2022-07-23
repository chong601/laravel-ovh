<?php

namespace App\Console\Commands\SoYouStart\DedicatedServer\Install;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class StartInstallation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:dedicatedserver:install:startinstallation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demonstrate start of installation';

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

        $serviceName = 'ns5006477.ip-192-99-148.net';
        $installationDetails = [
            'customHostname' => 'SySDemoInstall'
        ];
        $templateName = '20c56a28-ef47-4b8a-a1c3-da6fc241f364';
        $partitionSchemeName = '4c13de79-df62-4401-b0a7-5d19dc5980d1';

        $result = $ovh_api->dedicatedServer->install->start($serviceName, $templateName, $installationDetails, $partitionSchemeName);

        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return 0;
    }
}
