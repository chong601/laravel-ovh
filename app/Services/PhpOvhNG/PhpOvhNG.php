<?php
namespace App\Services\PhpOvhNG;

use App\Services\PhpOvhNG\Exceptions\InvalidParameterException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class PhpOvhNG
{
    /**
     * Url to communicate with Ovh API
     *
     * @var array
     */
    private array $endpoints = [
        'ovh-eu'        => 'https://eu.api.ovh.com/1.0',
        'ovh-ca'        => 'https://ca.api.ovh.com/1.0',
        'ovh-us'        => 'https://api.us.ovhcloud.com/1.0',
        'kimsufi-eu'    => 'https://eu.api.kimsufi.com/1.0',
        'kimsufi-ca'    => 'https://ca.api.kimsufi.com/1.0',
        'soyoustart-eu' => 'https://eu.api.soyoustart.com/1.0',
        'soyoustart-ca' => 'https://ca.api.soyoustart.com/1.0',
        'runabove-ca'   => 'https://api.runabove.com/1.0',
    ];

    /**
     * Contain endpoint selected to choose API
     *
     * @var ?string
     */
    private ?string $endpoint;

    /**
     * Contain key of the current application
     *
     * @var ?string
     */
    private ?string $application_key;

    /**
     * Contain secret of the current application
     *
     * @var ?string
     */
    private ?string $application_secret;

    /**
     * Contain consumer key of the current application
     *
     * @var ?string
     */
    private ?string $consumer_key;

    /**
     * Contain delta between local timestamp and api server timestamp
     *
     * @var ?string
     */
    private ?string $time_delta;

    /**
     * Contain http client connection
     *
     * @var ?Client
     */
    private ?Client $http_client;

    /**
     * Construct a new wrapper instance
     *
     * @param string $application_key    key of your application.
     *                                   For OVH APIs, you can create a application's credentials on
     *                                   https://api.ovh.com/createApp/
     * @param string $application_secret secret of your application.
     * @param string $api_endpoint       name of api selected
     * @param string $consumer_key       If you have already a consumer key, this parameter prevent to do a
     *                                   new authentication
     * @param Client $http_client        instance of http client
     *
     * @throws Exceptions\InvalidParameterException if one parameter is missing or with bad value
     */
    public function __construct(
        $application_key,
        $application_secret,
        $api_endpoint,
        $consumer_key = null,
        Client $http_client = null
    ) {
        if (!isset($api_endpoint)) {
            throw new Exceptions\InvalidParameterException("Endpoint parameter is empty");
        }

        if (preg_match('/^https?:\/\/..*/', $api_endpoint)) {
            $this->endpoint         = $api_endpoint;
        } else {
            if (!array_key_exists($api_endpoint, $this->endpoints)) {
                throw new Exceptions\InvalidParameterException("Unknown provided endpoint");
            }

            $this->endpoint       = $this->endpoints[$api_endpoint];
        }

        if (!isset($http_client)) {
            // Make Client mockable using Laravel app helper functionality
            $http_client = app(Client::class, ['config' => [
                'timeout'         => 30,
                'connect_timeout' => 5,
            ]]);
        }

        $this->application_key    = $application_key;
        $this->application_secret = $application_secret;
        $this->http_client        = $http_client;
        $this->consumer_key       = $consumer_key;
    }

    /**
     * Calculate time delta between local machine and API's server
     *
     * @throws ClientException if http request is an error
     * @return int
     */
    private function calculateTimeDelta()
    {
        if (!isset($this->time_delta)) {
            $response         = $this->rawCall(
                'GET',
                "/auth/time",
                null,
                false
            );
            $serverTimestamp  = (int)(string)$response->getBody();
            $this->time_delta = $serverTimestamp - (int)\time();
        }

        return $this->time_delta;
    }

    /**
     * Request a consumer key from the API and the validation link to
     * authorize user to validate this consumer key
     *
     * @param array $accessRules list of rules your application need.
     * @param null $redirection url to redirect on your website after authentication
     *
     * @return mixed
     * @throws InvalidParameterException
     * @throws \JsonException
     */
    public function requestCredentials(
        array $accessRules,
        $redirection = null
    ) {
        $parameters              = new \StdClass();
        $parameters->accessRules = $accessRules;
        $parameters->redirection = $redirection;

        //bypass authentication for this call
        $response = $this->decodeResponse(
            $this->rawCall(
                'POST',
                '/auth/credential',
                $parameters,
                true
            )
        );

        $this->consumer_key = $response["consumerKey"];

        return $response;
    }

    /**
     * This is the main method of this wrapper. It will
     * sign a given query and return its result.
     *
     * @param string $method HTTP method of request (GET,POST,PUT,DELETE)
     * @param string $path relative url of API request
     * @param \stdClass|array|null $content body of the request
     * @param bool $is_authenticated if the request use authentication
     *
     * @param null $headers
     * @return ResponseInterface
     * @throws Exceptions\InvalidParameterException
     * @throws GuzzleException
     * @throws \JsonException
     */
    protected function rawCall($method, $path, $content = null, $is_authenticated = true, $headers = null, bool $async = false)
    {
        if ($is_authenticated) {
            if (!isset($this->application_key)) {
                throw new Exceptions\InvalidParameterException("Application key parameter is empty");
            }

            if (!isset($this->application_secret)) {
                throw new Exceptions\InvalidParameterException("Application secret parameter is empty");
            }
        }

        $url     = $this->endpoint . $path;
        $request = new Request($method, $url);
        if (isset($content) && $method === 'GET') {
            $query_string = $request->getUri()->getQuery();

            $query = array();
            if (!empty($query_string)) {
                $queries = explode('&', $query_string);
                foreach ($queries as $element) {
                    $key_value_query = explode('=', $element, 2);
                    $query[$key_value_query[0]] = $key_value_query[1];
                }
            }

            $query = array_merge($query, (array)$content);

            // rewrite query args to properly dump true/false parameters
            foreach ($query as $key => $value) {
                if ($value === false) {
                    $query[$key] = "false";
                } elseif ($value === true) {
                    $query[$key] = "true";
                }
            }

            $query = \GuzzleHttp\Psr7\Query::build($query);

            $url     = $request->getUri()->withQuery($query);
            $request = $request->withUri($url);
            $body    = "";
        } elseif (isset($content)) {
            $body = json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

            $request->getBody()->write($body);
        } else {
            $body = "";
        }
        if (!is_array($headers)) {
            $headers = [];
        }
        $headers['Content-Type']      = 'application/json; charset=utf-8';

        if ($is_authenticated) {
            $headers['X-Ovh-Application'] = $this->application_key;

            if (!isset($this->time_delta)) {
                $this->calculateTimeDelta();
            }
            $now = time() + $this->time_delta;

            $headers['X-Ovh-Timestamp'] = $now;

            if (isset($this->consumer_key)) {
                $toSign                     = $this->application_secret . '+' . $this->consumer_key . '+' . $method
                    . '+' . $url . '+' . $body . '+' . $now;
                $signature                  = '$1$' . sha1($toSign);
                $headers['X-Ovh-Consumer']  = $this->consumer_key;
                $headers['X-Ovh-Signature'] = $signature;
            }
        }
        if ($async) {
            return $this->http_client->sendAsync($request, ['headers' => $headers]);
        } else {
            /** @var Response $response */
            return $this->http_client->send($request, ['headers' => $headers]);
        }
    }

    /**
     * Perform asynchronous requests with the provided array of promises
     *
     * @param PhpOvhAsyncRequest $promises The generated async object from PhpOvhAsyncRequest class
     * @return array
     * @throws Throwable Any form of exception during the processing of the promises
     */
    public function callAsync(PhpOvhAsyncRequest $promises): array
    {
        return Utils::settle($promises->toArray())->wait();
    }

    /**
     * Decode a Response object body to an Array
     *
     * @param Response $response
     *
     * @throws \JsonException
     */
    private function decodeResponse(Response $response)
    {
        return json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Wrap call to Ovh APIs for GET requests
     *
     * @param string $path path ask inside api
     * @param array $content content to send inside body of request
     * @param array  headers  custom HTTP headers to add on the request
     * @param bool   is_authenticated   if the request need to be authenticated
     *
     * @throws ClientException if http request is an error
     * @throws \JsonException
     */
    public function get($path, $content = null, $headers = null, $is_authenticated = true, $async = false)
    {
        if (preg_match('/^\/[^\/]+\.json$/', $path)) {
            // Schema description must be access without authentication
            return $this->decodeResponse(
                $this->rawCall("GET", $path, $content, false, $headers)
            );
        }

        if ($async) {
            return $this->rawCall("GET", $path, $content, $is_authenticated, $headers, true);
        } else {
            return $this->decodeResponse(
                $this->rawCall("GET", $path, $content, $is_authenticated, $headers)
            );
        }
    }

    /**
     * Wrap call to Ovh APIs for POST requests
     *
     * @param string $path    path ask inside api
     * @param array  $content content to send inside body of request
     * @param array  headers  custom HTTP headers to add on the request
     * @param bool   is_authenticated   if the request need to be authenticated
     *
     * @throws ClientException if http request is an error
     */
    public function post($path, $content = null, $headers = null, $is_authenticated = true, $async = false)
    {
        if ($async) {
            return $this->rawCall("POST", $path, $content, $is_authenticated, $headers, true);
        } else {
            return $this->decodeResponse(
                $this->rawCall("POST", $path, $content, $is_authenticated, $headers)
            );
        }
    }

    /**
     * Wrap call to Ovh APIs for PUT requests
     *
     * @param string $path    path ask inside api
     * @param array  $content content to send inside body of request
     * @param array  headers  custom HTTP headers to add on the request
     * @param bool   is_authenticated   if the request need to be authenticated
     *
     * @throws ClientException if http request is an error
     */
    public function put($path, $content, $headers = null, $is_authenticated = true, $async = false)
    {
        if ($async) {
            return $this->rawCall("PUT", $path, $content, $is_authenticated, $headers, true);
        } else {
            return $this->decodeResponse(
                $this->rawCall("PUT", $path, $content, $is_authenticated, $headers)
            );
        }
    }

    /**
     * Wrap call to Ovh APIs for DELETE requests
     *
     * @param string $path path ask inside api
     * @param array|null $content content to send inside body of request
     * @param array|null $headers custom HTTP headers to add on the request
     * @param bool $is_authenticated if the request need to be authenticated
     *
     * @throws ClientException if http request is an error
     * @throws \JsonException
     */
    public function delete(string $path, array $content = null, array $headers = null, bool $is_authenticated = true, $async = false)
    {
        if ($async) {
            return $this->rawCall("DELETE", $path, $content, $is_authenticated, $headers, true);
        } else {
            return $this->decodeResponse(
                $this->rawCall("DELETE", $path, $content, $is_authenticated, $headers)
            );
        }
    }

    /**
     * Get the current consumer key
     */
    public function getConsumerKey(): ?string
    {
        return $this->consumer_key;
    }

    /**
     * Return instance of http client
     */
    public function getHttpClient(): ?Client
    {
        return $this->http_client;
    }
}
