<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartUpdateUserTemplatePartitionSchemeName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:updateusertemplatepartitionschemename {user_template_name} {partition_scheme_name} {new_partition_scheme_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user template partition scheme name';

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
        $newPartitionSchemeName = $this->argument('new_partition_scheme_name');

        $ovh_api->putUpdateUserDefinedTemplatePartitionScheme($userTemplateName, $partitionSchemeName, $newPartitionSchemeName, null);
        $this->info("Successfully changed $partitionSchemeName to $newPartitionSchemeName for $userTemplateName");
        return 0;
    }
}
