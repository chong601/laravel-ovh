<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartDeleteUserDefinedTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:me:installationtemplate:delete {user_template_name : Name of the user template to be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an user-defined installation template';

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

        try {
            $ovh_api->deleteUserDefinedTemplate($userTemplateName);
            $this->info(sprintf('User template %s deleted successfully!', $userTemplateName));
        } catch (Exception $e) {
            $this->error(sprintf('Failed deleting user template %s due to %s', $userTemplateName, $e->getMessage()));
            return 1;
        }

        return 0;
    }
}
