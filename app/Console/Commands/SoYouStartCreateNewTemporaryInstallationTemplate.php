<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Ramsey\Uuid\Uuid;

class SoYouStartCreateNewTemporaryInstallationTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:me:usertemplate:partitionscheme:mountpoint:createtemporary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a temporary user template (installation template simulation)';

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

        $userTemplateName = Uuid::uuid4()->toString();
        $partitionSchemeName = Uuid::uuid4()->toString();
        $dedicatedTemplateName = 'ubuntu2004-server_64';

        // Create a temporary user template based on a dedicate template
        $ovh_api->postCreateNewUserDefinedTemplate($userTemplateName, $dedicatedTemplateName);

        // Create a partition scheme name
        $ovh_api->postCreateNewUserDefinedTemplatePartitionScheme($userTemplateName, $partitionSchemeName, 1);

        // Create a list of filesystems to use
        $ovh_api->postCreateNewUserDefinedTemplatePartitionSchemeMountpoint($userTemplateName, $partitionSchemeName,
        'xfs', '/boot', 1, 10240, 0, 'primary', '');
        $ovh_api->postCreateNewUserDefinedTemplatePartitionSchemeMountpoint($userTemplateName, $partitionSchemeName,
        'xfs', '/', 1, 0, 1, 'primary', '');

        return 0;
    }
}
