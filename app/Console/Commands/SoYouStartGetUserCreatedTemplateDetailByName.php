<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartGetUserCreatedTemplateDetailByName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:getusertemplatedetail {user_template_name : User template name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $userTemplateDetails = $ovh_api->getUserDefinedInstallationTemplateDetails($userTemplateName);
        $userTemplatePartitionSchemes = $ovh_api->getUserDefinedInstallationTemplatePartitionSchemes($userTemplateName);
        foreach ($userTemplatePartitionSchemes as $userTemplatePartitionScheme) {
            // Get partition details
            $userTemplatePartitionMountPoints = $ovh_api->getUserDefinedInstallationTemplatePartitionMountpoints($userTemplateName, $userTemplatePartitionScheme);
            $userTemplatePartitionMountPointsList = [];
            // Get mount point details
            foreach ($userTemplatePartitionMountPoints as $userTemplatePartitionMountPoint) {
                $userTemplatePartitionMountPointDetail = $ovh_api->getUserDefinedInstallationTemplatePartitionMountpointDetails($userTemplateName, $userTemplatePartitionScheme, $userTemplatePartitionMountPoint);
                $userTemplatePartitionMountPointsList[] = $userTemplatePartitionMountPointDetail;
            }
            $userTemplatePartitionSchemesList[$userTemplatePartitionScheme] = $userTemplatePartitionMountPointsList;
        }
        $userTemplateList = [
            'template_details' => $userTemplateDetails,
            'partition_schemes' => $userTemplatePartitionSchemesList
        ];

        print(json_encode($userTemplateList, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return 0;
    }
}
