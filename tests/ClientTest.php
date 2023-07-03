<?php
require 'vendor/autoload.php';

use Application\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    // Test Access Token
    public function testGetToken()
    {
        $app_id = "";
        $app_secret = "";

        $dataopenClient = new Client($app_id, $app_secret);
        $dataopenClient->get_token();

        echo "is_authenticated", $dataopenClient->is_authenticated();
        
        $this->assertNotNull($dataopenClient->is_authenticated());
    }

    // Test Request GET
    public function testGetRequest()
    {
        $app_id = "";
        $app_secret = "";

        $dataopenClient = new Client($app_id, $app_secret);

        $headers = [];

        $params = [
            "app" => 46,
            "page_size" => 1,
            "page" => 1,
        ];

        $body = [];

        $res = $dataopenClient->request("/libra/openapi/v1/open/flight-list", "GET", $headers, $params, $body);

        echo "\n\nOutput: ", json_encode($res);
    }

    // Test Request POST
    public function testPostRequest()
    {
        $app_id = "";
        $app_secret = "";

        $dataopenClient = new Client($app_id, $app_secret);

        $headers = [];

        $params = [];

        $body = [
            "uid_list" => ["1111111110000"],
        ];

        $res = $dataopenClient->request(
            "/libra/openapi/v1/open/flight/version/6290880/add-test-user",
            "POST",
            $headers,
            $params,
            $body,
        );

        echo "\n\nOutput: ", json_encode($res);

        // Add any assertions to validate response here
    }

    // Test Request POST
    public function testMaterialPostRequest()
    {
        $app_id = "";
        $app_secret = "";

        $dataopenClient = new Client($app_id, $app_secret, "https://analytics.volcengineapi.com",
        "dataopen_staging", null);

        $headers = [];

        $params = [];

        $body = [
            "name" => "ccnnodetest",
            "title" => "测试title",
            "type" => "component",
            "description" => "测试description2",
            "frameworkType" => "react",
        ];

        $res = $dataopenClient->request(
            "/material/openapi/v1/material",
            "PUT",
            $headers,
            $params,
            $body,
        );

        echo "\n\nOutput testMaterialPostRequest: ", json_encode($res);
    }
}