<?php
namespace App\Services\Kimsufi;

use Ovh\Api;

/**
 * A class that provice base functionality to interact with OVH API.
 *
 * It is recommended to extend the functionality outside of this service for specific use cases to keep the implementation clean.
 */
class KimsufiService {

    /** @var \Ovh\Api */
    protected $ovh_api;

    public function __construct(string $application_key, string $application_secret, string $endpoint, ?string $consumer_key)
    {
        $this->ovh_api = new Api($application_key, $application_secret, $endpoint, $consumer_key);
    }

    /**
     * Performs a GET request to OVH API based on route and parameters
     */
    public function get(string $route, array $parameters = []) {
        // TODO: Exception handling
        // What is content?
        if ($parameters) {
            return $this->ovh_api->get($route, $parameters);
        }
        return $this->ovh_api->get($route);
    }

    /**
     * Performs a POST request to OVH API based on route and parameters
     */
    public function post(string $route, array $parameters = []) {
        // TODO: Exception handling
        // What is content?
        if ($parameters) {
            return $this->ovh_api->post($route, $parameters);
        }
        return $this->ovh_api->post($route);
    }

    /**
     * Performs a DELETE request to OVH API based on route and parameters
     */
    public function delete(string $route, array $parameters = []) {
        // TODO: Exception handling
        // What is content?
        if ($parameters) {
            return $this->ovh_api->delete($route, $parameters);
        }
        return $this->ovh_api->delete($route);
    }

    /**
     * Performs a PUT request to OVH API based on route and parameters
     */
    public function put(string $route, array $parameters = []) {
        // TODO: Exception handling
        // What is content?
        if ($parameters) {
            return $this->ovh_api->put($route, $parameters);
        }
        return $this->ovh_api->put($route, $parameters);
    }
}
