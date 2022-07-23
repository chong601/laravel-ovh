<?php

namespace App\Console\Commands\SoYouStart\DedicatedServer;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class GetAuthenticationSecret extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:dedicatedserver:getauthenticationsecret {serviceName : Service name to retrieve the secret}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get an URL containing the secret to access your server';

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

        $serviceName = $this->argument('serviceName');

        $result = $ovh_api->dedicatedServer->authenticationSecret($serviceName);

        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info(sprintf('The password can be retrieved at https://www.ovh.com/secret-as-a-service-ui/#!/secret-retrieve?id=%s. The password will expire within one week!', $result[0]['password']));
        $this->info('Pro tip: Use SSH keys for future installations!');
        return 0;
    }
}
