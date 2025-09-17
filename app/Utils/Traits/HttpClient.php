<?php

namespace App\Utils\Traits;

use GuzzleHttp\Client;

trait HttpClient
{
    public function httpGet($url, $needDecode = true)
    {
        $client = new Client();
        $response = $client->request('GET', $url);
        return $needDecode ? json_decode((string) $response->getBody(), true) : $response->getBody();
    }

    public function httpPost($url, $data, $needDecode = true, array $headers = [])
    {
        $client = new Client();
        $options = [
            'json' => $data
        ];
        if (!empty($headers)) {
            $options['headers'] = $headers;
        }

        $response = $client->request('POST', $url, $options);

        return $needDecode ? json_decode((string) $response->getBody(), true) : $response->getBody();
    }

    public function httpPostStream($url, $data, callable $callback, array $headers = [])
    {
        $client = new Client(['stream' => true, 'timeout' => 0]);
        $options = [
            'json' => $data,
            'stream' => true,
        ];
        if (!empty($headers)) {
            $options['headers'] = $headers;
        }

        $response = $client->request('POST', $url, $options);
        $body = $response->getBody();

        while (!$body->eof()) {
            $chunk = $body->read(1024);
            if ($chunk !== '') {
                $callback($chunk);
            }
        }
    }
}
