<?php

namespace App\Utils\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

trait HttpClient
{
    public function httpGet($url, $needDecode = true, $headers = null)
    {
        $client = new Client();
        $response = !is_null($headers) ?  $client->request('GET', $url, ['headers' => $headers]) : $client->request('GET', $url);
        return $needDecode ? json_decode((string) $response->getBody(), true) : $response->getBody();
    }

    public function httpPost($url, $data, $dataType = 1, $needDecode = true)
    {
        $client = new Client();

        switch ($dataType) {
            case 1:
                $response = $client->request('POST', $url, ['json' => $data]);
                break;

            case 2:
                $response = $client->request('POST', $url, ['form_params' => $data]);
                break;

            case 3:
                $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
                $response = $client->request('POST', $url, ['body' => $jsonData, 'headers' => ['Content-Type' => 'application/json']]);
                break;
        }

        return $needDecode ? json_decode((string)$response->getBody(), true) : $response->getBody();
    }

    /**
     * 简化的流式请求方法（返回生成器）
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \Generator
     * @throws \Exception
     */
    public function httpPostStreamGenerator($url, $data, array $headers = [])
    {
        $client = new Client();

        $options = [
            'json' => $data,
            'stream' => true,
            'headers' => array_merge([
                'Accept' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
            ], $headers),
        ];

        try {
            $response = $client->request('POST', $url, $options);
            $body = $response->getBody();

            $buffer = '';
            while (!$body->eof()) {
                $chunk = $body->read(1024);
                $buffer .= $chunk;

                $lines = explode("\n", $buffer);
                $buffer = array_pop($lines);

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    if (strpos($line, 'data: ') === 0) {
                        $jsonData = substr($line, 6);
                        if ($jsonData === '[DONE]') continue;

                        $decodedData = json_decode($jsonData, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            yield $decodedData;
                        }
                    }
                }

                usleep(10000);
            }

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse();
                $errorBody = (string)$errorResponse->getBody();
                $errorData = json_decode($errorBody, true);

                throw new \Exception(
                    $errorData['error'] ?? $errorData['message'] ?? 'HTTP请求失败: ' . $e->getMessage(),
                    $e->getCode()
                );
            }

            throw new \Exception('HTTP请求失败: ' . $e->getMessage(), $e->getCode());
        }
    }
}
