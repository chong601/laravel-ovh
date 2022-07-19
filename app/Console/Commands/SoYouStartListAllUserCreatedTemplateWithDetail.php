<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartListAllUserCreatedTemplateWithDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:me:installationtemplate:list:detail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all user-created templates with expanded details';

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

        // Get all supported OS templates for the server
        // This also includes personal templates
        $userTemplates = $ovh_api->me->installationTemplate->all();

        $userTemplateList = [];
        foreach ($userTemplates as $userTemplate) {
            $userTemplateDetails = $ovh_api->me->installationTemplate->get($userTemplate);
            $userTemplatePartitionSchemes = $ovh_api->me->installationTemplate->partitionScheme->all($userTemplate);
            $userTemplatePartitionSchemesList = [];
            foreach ($userTemplatePartitionSchemes as $userTemplatePartitionScheme) {
                // Get partition details
                $userTemplatePartitionMountPoints = $ovh_api->me->installationTemplate->partitionScheme->partition->all($userTemplate, $userTemplatePartitionScheme);
                $userTemplatePartitionMountPointsList = [];
                // Get mount point details
                foreach ($userTemplatePartitionMountPoints as $userTemplatePartitionMountPoint) {
                    $userTemplatePartitionMountPointDetail = $ovh_api->me->installationTemplate->partitionScheme->partition->get($userTemplate, $userTemplatePartitionScheme, $userTemplatePartitionMountPoint);
                    $userTemplatePartitionMountPointsList[] = $userTemplatePartitionMountPointDetail;
                }
                $userTemplatePartitionSchemesList[$userTemplatePartitionScheme] = $userTemplatePartitionMountPointsList;
            }
            $userTemplateList[$userTemplate] = [
                'template_details' => $userTemplateDetails,
                'partition_schemes' => $userTemplatePartitionSchemesList
            ];
        }

        print(json_encode($userTemplateList, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return 0;
    }
}
