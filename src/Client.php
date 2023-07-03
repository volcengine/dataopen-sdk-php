<?php

namespace Application;

/*
 * Copyright 2023 DataOpen SDK Authors
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require 'vendor/autoload.php';
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request;

class Client
{
    const METHOD_ALLOWED = ["POST", "GET", "DELETE", "PUT", "PATCH"];
    const OPEN_APIS_PATH = "/open-apis";

    private $app_id;
    private $app_secret;
    private $url;
    private $env;
    private $expiration;
    private $_access_token;
    private $_ttl;
    private $_token_time;
    private $http_client;

    public function __construct(
        $app_id,
        $app_secret,
        $url = "https://analytics.volcengineapi.com",
        $env = "dataopen",
        $expiration = "1800"
    ) {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        $this->url = $url;
        $this->env = $env;
        $this->expiration = $expiration;
        $this->_ttl = 0;
        $this->_access_token = "";
        $this->_token_time = 0;
        $this->http_client = new HttpClient(['base_uri' => $this->url]);
    }

    public function request($service_url, $method, $headers, $params, $body)
    {
        $method = strtoupper($method);

        if (!$this->_access_token || !$this->_valid_token()) {
            $this->get_token();
        }

        $new_headers = [
            'Authorization' => $this->_access_token,
            'Content-Type' => 'application/json',
        ];
        $new_headers = array_merge($new_headers, $headers);

        $completed_url = $this->url . "/" . $this->env . self::OPEN_APIS_PATH . $service_url;
        $query_url = $this->_joint_query($completed_url, $params);

        $options = [
            'headers' => $new_headers,
            'json' => $body,
        ];

        $response = $this->http_client->request($method, $query_url, $options);

        $resp = json_decode($response->getBody(), true);

        if ($response->getStatusCode() != 200) {
            throw new Exception("Invalid request method");
        }

        return $resp;
    }

    public function get_token()
    {
        $authorization_url = $this->env . self::OPEN_APIS_PATH . "/v1/authorization";
        $completed_url = $this->url . "/" . $authorization_url;

        $map = [
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
        ];

        $response = $this->http_client->request('POST', $completed_url, [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $map,
        ]);

        $resp = json_decode($response->getBody(), true);

        $token_time = time();

        if ($resp['code'] == 200 && isset($resp['data'])) {
            $this->_ttl = $resp['data']['ttl'];
            $this->_token_time = $token_time;
            $this->_access_token = $resp['data']['access_token'];
        }
    }

    public function is_authenticated() {
        return !!$this->_access_token;
    }

    private function _joint_query($url, $params)
    {
        return $url . '?' . http_build_query($params);
    }

    private function _valid_token()
    {
        $current_time = time();

        if ($current_time - $this->_token_time > $this->_ttl) {
            return false;
        }

        return true;
    }
}