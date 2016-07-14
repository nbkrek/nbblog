<?php

class SSLTest extends PHPUnit_Framework_TestCase 
{

    public function testSwitchToSSL() {
        // create a new cURL resource
        $c = new \Curl\Curl();

        $c->setopt(CURLOPT_CONNECTTIMEOUT, 3);

        // set URL and other appropriate options
        $c->get("http://" . $_SERVER['http_host'] . "/");

        $this->assertTrue(is_array($c->response_headers));

        $headers = implode($c->response_headers, '');
        $this->assertTrue(strpos($headers, 'Location: https') !== false);
    }

}
