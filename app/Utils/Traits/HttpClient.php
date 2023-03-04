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

    public function httpPost($url, $data, $needDecode = true)
    {
        $client = new Client();
        $response = $client->request('POST', $url, ['json' => $data]);
        return $needDecode ? json_decode((string) $response->getBody(), true) : $response->getBody();
    }
}
