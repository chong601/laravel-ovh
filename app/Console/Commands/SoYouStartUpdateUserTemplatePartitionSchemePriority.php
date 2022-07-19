<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartUpdateUserTemplatePartitionSchemePriority extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:updateusertemplatepartitionschemepriority {user_template_name} {partition_scheme_name} {new_priority}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user template partition scheme priority';

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

        // {user_template_name} {partition_scheme_name} {new_priority}
        $userTemplateName = $this->argument('user_template_name');
        $partitionSchemeName = $this->argument('partition_scheme_name');
        $newPriority = $this->argument('new_priority');

        $ovh_api->me->installationTemplate->partitionScheme->update($userTemplateName, $partitionSchemeName, null, $newPriority);
        $this->info("Successfully updated $partitionSchemeName's priority for $userTemplateName");
        return 0;
    }
}
