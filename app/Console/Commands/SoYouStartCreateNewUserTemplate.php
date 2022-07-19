<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartCreateNewUserTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:createnewusertemplate {user_template_name : Name of the new template name} {dedicated_template_name : Which template name it should be created from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new user template based on the provided SoYouStart supported template name';

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

        $userTemplateName = $this->argument('user_template_name');
        $dedicatedTemplateName = $this->argument('dedicated_template_name');

        try {
            $ovh_api->me->installationTemplate->create($userTemplateName, 'en', $dedicatedTemplateName);
        } catch (GuzzleException $e) {
            $message = $e->getMessage();
            print("Unable to create $userTemplateName template due to $message");
            return 1;
        }

        print("New template $userTemplateName created!\n");
        return 0;
    }
}
