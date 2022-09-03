<?php

namespace App\Console\Commands;

use App\Services\SoYouStart\SoYouStartService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Ovh\Api;

class TestAsyncDecodeCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:guzzlehttp:async';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[DEVELOPMENT USE ONLY] Simulate asynchronous GuzzleHTTP request and response';

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
        $client = new Client();
        $baseUrl = 'https://ca.api.soyoustart.com/1.0';

        $requests = [
            'server' => [
                'method' => 'GET',
                'path' => '/dedicated/server',
                'content' => null
            ],
            'ip' => [
                'method' => 'GET',
                'path' => '/ip',
                'content' => null
            ]
        ];
        // Configure headers
        $headers = [];
        $contentType = 'application/json; charset=utf-8';
        $applicationKey = config('soyoustart.application_key');
        $applicationSecret = config('soyoustart.application_secret');
        $consumerKey = config('soyoustart.consumer_key');

        // Signature will be generated later
        $promises = [];
        foreach ($requests as $key => $value) {
            $now = Carbon::now()->getTimestamp();
            $headers['X-Ovh-Application'] = $applicationKey;
            $headers['X-Ovh-Timestamp'] = $now;
            $headers['X-Ovh-Consumer'] = $consumerKey;
            $toSign = sprintf('%s+%s+%s+%s+%s+%s',
                $applicationSecret,
                $consumerKey,
                $value['method'],
                $baseUrl . $value['path'],
                json_encode($value['content']),
                // We cheat.
                $now
            );
            $headers['X-Ovh-Signature'] = '$1$' . sha1($toSign);
            $request = new Request($value['method'], $baseUrl . $value['path'], $headers);
            $request->getBody()->write(json_encode($value['content']));
            $promises[$key] = $client->sendAsync($request, ['headers']);
        }
        $responses = Utils::settle($promises)->wait();
        print_r($responses);
        foreach ($responses as $response) {
//            var_dump($responses);
            $this->info($response['value']->getBody());
        }
        return 0;
    }
}
