<?php
namespace App\Services\SoYouStart;

use Ovh\Api;

/**
 * A class that provice base functionality to interact with OVH API.
 *
 * It is recommended to extend the functionality outside of this service for specific use cases to keep the implementation clean.
 */
class SoYouStartService {

    /** @var \Ovh\Api */
    protected $ovh_api;

    public function __construct()
    {
        //
    }

    /**
     * Initialize OVH API class.
     *
     * All applications MUST call this function before issuing any function calls.
     * Failure to do so may result on errors.
     */
    public function setOvhConfiguration(
        $application_key = config('ovh.application_key'),
        $application_secret = config('ovh.application_secret'),
        $consumer_key = config('ovh.consumer_key'),
        $endpoint = 'soyoustart-ca'
    ) {
        $this->ovh_api = new Api($application_key, $application_secret, $endpoint, $consumer_key);
        return $this;
    }

    /**
     * Performs a GET request to OVH API based on route and parameters
     */
    public function get($route, $parameters) {
        // TODO: Exception handling
        // What is content?
        return $this->ovh_api->get($route, $parameters);
    }

    /**
     * Performs a POST request to OVH API based on route and parameters
     */
    public function post($route, $parameters) {
        // TODO: Exception handling
        // What is content?
        return $this->ovh_api->post($route, $parameters);
    }

    /**
     * Performs a DELETE request to OVH API based on route and parameters
     */
    public function delete($route, $parameters) {
        // TODO: Exception handling
        // What is content?
        return $this->ovh_api->delete($route, $parameters);
    }

    /**
     * Performs a PUT request to OVH API based on route and parameters
     */
    public function put($route, $parameters) {
        // TODO: Exception handling
        // What is content?
        return $this->ovh_api->put($route, $parameters);
    }
}
