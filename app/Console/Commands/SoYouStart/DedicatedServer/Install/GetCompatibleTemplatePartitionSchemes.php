<?php

namespace App\Console\Commands\SoYouStart\DedicatedServer\Install;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class GetCompatibleTemplatePartitionSchemes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:dedicatedserver:install:getcompatibletemplatepartitionschemes
                            {serviceName : Service name}
                            {templateName : Template name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get compatible partition schemes usable from installation templates';

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
        $templateName = $this->argument('templateName');

        $result = $ovh_api->dedicatedServer->install->compatibleTemplatePartitionSchemes($serviceName, $templateName);

        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
