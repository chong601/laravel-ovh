<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartListAllUserCreatedTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:listallusercreatedtemplates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all user-created templates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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
        $userTemplates = $ovh_api->get('/me/installationTemplate', []);

        $userTemplateList = [];
        foreach ($userTemplates as $userTemplate) {
            $userTemplateDetails = $ovh_api->get(sprintf('/me/installationTemplate/%s', $userTemplate), []);
            $userTemplatePartitionSchemes = $ovh_api->get(sprintf('/me/installationTemplate/%s/partitionScheme', $userTemplate), []);
            $userTemplatePartitionSchemesList = [];
            foreach ($userTemplatePartitionSchemes as $userTemplatePartitionScheme) {
                // Get partition details
                $userTemplatePartitionMountPoints = $ovh_api->get(sprintf('/me/installationTemplate/%s/partitionScheme/%s/partition', $userTemplate, $userTemplatePartitionScheme), []);
                $userTemplatePartitionMountPointsList = [];
                // Get mount point details
                foreach ($userTemplatePartitionMountPoints as $userTemplatePartitionMountPoint) {
                    $userTemplatePartitionMountPointDetail = $ovh_api->get(sprintf('/me/installationTemplate/%s/partitionScheme/%s/partition/%s', $userTemplate, $userTemplatePartitionScheme, urlencode($userTemplatePartitionMountPoint)), []);
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
