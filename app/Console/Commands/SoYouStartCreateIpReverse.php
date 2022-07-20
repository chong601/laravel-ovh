<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SoYouStartCreateIpReverse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soyoustart:ip:reverse:create
                            {ip : IP block}
                            {ipReverse : IP address to set the reverse domain}
                            {reverse : The reverse domain name to set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a reverse domain to an IP';

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
        $reverse = $this->argument('reverse');

        $result = $ovh_api->ip->reverse->create($ip, $ipReverse, $reverse);

        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return 0;
    }
}
