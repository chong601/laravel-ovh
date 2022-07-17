<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartUpdateUserTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:me:installationtemplate:update
                            {template_name : Name of the template to update}
                            {--customHostname= : The custom hostname to use on this template}
                            {--postInstallationScriptLink= : The link to the post-installation script}
                            {--postInstallationScriptReturn= : The expected return data when the script successfully ran}
                            {--sshKeyName= : The SSH key name to use on this template}
                            {--useDistributionKernel= : Whether to use distribution-provided kernel}
                            {--defaultLanguage= : The default language to use for the installation}
                            {--updatedTemplateName= : Template name to update to}';

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

        $templateName = $this->argument('template_name');

        $customHostname = $this->option('customHostname');
        $postInstallationScriptLink = $this->option('postInstallationScriptLink');
        $postInstallationScriptReturn = $this->option('postInstallationScriptReturn');
        $sshKeyName = $this->option('sshKeyName');
        $useDistributrionKernel = $this->option('useDistributionKernel');
        $defaultLanguage = $this->option('defaultLanguage');
        $updatedTemplateName = $this->option('updatedTemplateName');

        print(json_encode([$templateName, $customHostname, $postInstallationScriptLink, $postInstallationScriptReturn, $sshKeyName, $useDistributrionKernel, $defaultLanguage, $updatedTemplateName]));

        // $ovh_api->putUpdateUserDefinedTemplate(
        //     $templateName,
        //     $customHostname,
        //     $postInstallationScriptLink,
        //     $postInstallationScriptReturn,
        //     $sshKeyName,
        //     $useDistributrionKernel,
        //     $defaultLanguage,
        //     $updatedTemplateName
        // );


        return 0;
    }
}
