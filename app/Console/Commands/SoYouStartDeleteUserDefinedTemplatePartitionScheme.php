<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartDeleteUserDefinedTemplatePartitionScheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:me:installationtemplate:partitionscheme:delete
                            {user_template_name : Name of the user template to delete}
                            {partition_scheme_name : Name of the new partition scheme name to delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a partition scheme from a user-defined installation template';

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
            $ovh_api->deleteUserDefinedTemplatePartitionScheme($userTemplateName, $partitionSchemeName);
        } catch (Exception $e) {
            $this->error(sprintf('Unable to delete %s partition scheme from %s user installation template due to %s', $userTemplateName, $partitionSchemeName, $e->getMessage()));
        }

        $this->info(sprintf('Partition scheme %s deleted from %s installation template', $partitionSchemeName, $userTemplateName));

        return 0;
    }
}
