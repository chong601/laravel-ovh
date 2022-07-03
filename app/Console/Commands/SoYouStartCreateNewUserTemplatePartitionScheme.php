<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartCreateNewUserTemplatePartitionScheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:createusertemplatepartitionscheme {user_template_name : Name of the user template} {partition_scheme_name : Name of the new partition scheme name} {priority : Priority of this partition scheme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new partition scheme for a user template';

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
        $priority = $this->argument(('priority'));
        if (!is_numeric($priority)) {
            $this->error("Priority must be an integer!");
            return 1;
        }

        $ovh_api->postCreateNewUserDefinedTemplatePartitionScheme($userTemplateName, $partitionSchemeName, $priority);

        $this->info("Partition scheme $partitionSchemeName for $userTemplateName created successfully!");

        return 0;
    }
}
