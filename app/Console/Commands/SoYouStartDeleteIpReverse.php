<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartDeleteIpReverse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:ip:reverse:delete
                            {ip : IP block}
                            {ipReverse : IP address to set the reverse domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete reverse address from an IP block';

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

        $ip = $this->argument('ip');
        $ipReverse = $this->argument('ipReverse');

        $ovh_api->ip->reverse->delete($ip, $ipReverse);

        return 0;
    }
}
