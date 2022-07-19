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
            ['filesystem' => 'xfs', 'mountpoint' => '/test1', 'raid' => 1, 'size' => 10240, 'step' => 1, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test2', 'raid' => 1, 'size' => 10240, 'step' => 2, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test3', 'raid' => 1, 'size' => 10240, 'step' => 3, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test4', 'raid' => 1, 'size' => 10240, 'step' => 4, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test5', 'raid' => 1, 'size' => 10240, 'step' => 5, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test6', 'raid' => 1, 'size' => 10240, 'step' => 6, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test7', 'raid' => 1, 'size' => 10240, 'step' => 7, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test8', 'raid' => 1, 'size' => 10240, 'step' => 8, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test9', 'raid' => 1, 'size' => 10240, 'step' => 9, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test10', 'raid' => 1, 'size' => 10240, 'step' => 10, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test11', 'raid' => 1, 'size' => 10240, 'step' => 11, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test12', 'raid' => 1, 'size' => 10240, 'step' => 12, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test13', 'raid' => 1, 'size' => 10240, 'step' => 13, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test14', 'raid' => 1, 'size' => 10240, 'step' => 14, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test15', 'raid' => 1, 'size' => 10240, 'step' => 15, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/test16', 'raid' => 1, 'size' => 10240, 'step' => 16, 'type' => 'primary', 'volumeName' => ''],
            ['filesystem' => 'xfs', 'mountpoint' => '/', 'raid' => 1, 'size' => 0, 'step' => 17, 'type' => 'primary', 'volumeName' => ''],
        ];

        // Create a temporary user template based on a dedicate template
        $ovh_api->me->installationTemplate->create($userTemplateName, 'en', $dedicatedTemplateName);

        // Create a partition scheme name
        $ovh_api->me->installationTemplate->partitionScheme->create($userTemplateName, $partitionSchemeName, 1);

        // Create a list of filesystems to use
        foreach ($partitions as $partition) {
            $ovh_api->me->installationTemplate->partitionScheme->partition->create(
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
