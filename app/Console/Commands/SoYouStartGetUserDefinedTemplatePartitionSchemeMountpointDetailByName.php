<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartGetUserDefinedTemplatePartitionSchemeMountpointDetailByName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:me:installationtemplate:partitionscheme:partition:detail
                            {template_name : Installation template name}
                            {scheme_name : Partition scheme name}
                            {mountpoint : Mount point path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get mount point details';

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

        $templateName = $this->argument('template_name');
        $schemeName = $this->argument('scheme_name');
        $mountpoint = $this->argument('mountpoint');

        $result = $ovh_api->me->installationTemplate->partitionScheme->partition->get($templateName, $schemeName, $mountpoint);

        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
