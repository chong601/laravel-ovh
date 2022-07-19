<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartGetUserDefinedTemplatePartitionSchemeDetailByName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:me:installationtemplate:partitionscheme:detail
                            {user_template_name : Name of the user template}
                            {partition_scheme_name : Name of the new partition scheme name to delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a user-defined installation template partition scheme details';

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

        $userTemplateName = $this->argument('user_template_name');
        $partitionSchemeName = $this->argument('partition_scheme_name');

        try {
            $result = $ovh_api->me->installationTemplate->partitionScheme->get($userTemplateName, $partitionSchemeName);
        } catch (Exception $e) {
            $this->error(sprintf('Failed to get partition scheme details with error %s', $e->getMessage()));
            return 1;
        }

        $this->info(json_encode($result, JSON_PRETTY_PRINT));
        return 0;
    }
}
