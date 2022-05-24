<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class GetInstallationTemplateDetailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:getinstallationtemplatedetail {installation_template}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch installation template details based on the installation template name';

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

        $installationTemplate = $this->argument('installation_template');

        $result = $ovh_api->get(sprintf('/dedicated/installationTemplate/%s', $installationTemplate), '');

        print(json_encode($result, JSON_PRETTY_PRINT));
        return 0;
    }
}
