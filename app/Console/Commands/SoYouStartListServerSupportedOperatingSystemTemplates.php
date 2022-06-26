<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartListServerSupportedOperatingSystemTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:listserversupportedoperatingsystemtemplates {service_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $serviceName = $this->argument('service_name');

        // Get all supported OS templates for the server
        // This also includes personal templates
        $osTemplates = $ovh_api->get(sprintf('/dedicated/server/%s/install/compatibleTemplates', $serviceName), []);

        $osTemplateList = [];
        foreach ($osTemplates['ovh'] as $osTemplates) {
            $osTemplateDetails = $ovh_api->get(sprintf('/dedicated/installationTemplate/%s', $osTemplates), []);
            $osTemplatePartitionSchemes = $ovh_api->get(sprintf('/dedicated/server/%s/install/compatibleTemplatePartitionSchemes', $serviceName), ['templateName' => $osTemplates]);
            $osTemplatePartitionSchemesList = [];
            foreach ($osTemplatePartitionSchemes as $osTemplatePartitionScheme) {
                // Get partition details
                $osTemplatePartitionMountPoints = $ovh_api->get(sprintf('/dedicated/installationTemplate/%s/partitionScheme/%s/partition', $osTemplates, $osTemplatePartitionScheme), []);
                $osTemplatePartitionMountPointsList = [];
                // Get mount point details
                foreach ($osTemplatePartitionMountPoints as $osTemplatePartitionMountPoint) {
                    $osTemplatePartitionMountPointDetail = $ovh_api->get(sprintf('/dedicated/installationTemplate/%s/partitionScheme/%s/partition/%s', $osTemplates, $osTemplatePartitionScheme, urlencode($osTemplatePartitionMountPoint)), []);
                    $osTemplatePartitionMountPointsList[] = $osTemplatePartitionMountPointDetail;
                }
                $osTemplatePartitionSchemesList[$osTemplatePartitionScheme] = $osTemplatePartitionMountPointsList;
            }
            $osTemplateList[$osTemplates] = [
                'template_details' => $osTemplateDetails,
                'partition_schemes' => $osTemplatePartitionSchemesList
            ];
        }

        print(json_encode($osTemplateList, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
