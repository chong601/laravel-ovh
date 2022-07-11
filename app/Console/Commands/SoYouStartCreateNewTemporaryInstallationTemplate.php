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

        $partitions = [
            ['filesystem' => 'xfs', 'mountpoint' => '/boot', 'raid' => 1, 'size' => 10240, 'step' => 0, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/', 'raid' => 1, 'size' => 0, 'step' => 17, 'type' => 'primary', 'volumeName' => ''],
        ];

        // Create a temporary user template based on a dedicate template
        $ovh_api->postCreateNewUserDefinedTemplate($userTemplateName, $dedicatedTemplateName);

        // Create a partition scheme name
        $ovh_api->postCreateNewUserDefinedTemplatePartitionScheme($userTemplateName, $partitionSchemeName, 1);

        // Create a list of filesystems to use
        foreach ($partitions as $partition) {
            $ovh_api->postCreateNewUserDefinedTemplatePartitionSchemeMountpoint(
                $userTemplateName,
                $partitionSchemeName,
                $partition['filesystem'],
                $partition['mountpoint'],
                $partition['raid'],
                $partition['size'],
                $partition['step'],
                $partition['type'],
                $partition['volumeName']
            );
            $this->info(sprintf('Created %s mountpoint on %s user template %s partition scheme.', $partition['mountpoint'], $userTemplateName, $partitionSchemeName));
        }

        return 0;
    }
}
