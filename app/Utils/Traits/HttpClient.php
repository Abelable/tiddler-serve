<?php

namespace App\Utils\Traits;

use GuzzleHttp\Client;

trait HttpClient
{
    public function httpGet($url)
    {
        $client = new Client();
        $response = $client->request('GET', $url);
        return json_decode((string) $response->getBody(), true);
    }

    public function httpPost($url, $data)
    {
        $client = new Client();
        $response = $client->request('POST', $url, ['json' => $data]);
        return json_decode((string) $response->getBody(), true);
    }
}
