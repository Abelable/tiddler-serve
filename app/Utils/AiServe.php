<?php

namespace App\Utils;

use App\Utils\Traits\HttpClient;

class AiServe
{
    use HttpClient;

    const CHAT_MESSAGES_URL = '%s/v1/chat-messages';

    protected $apiKey;
    protected $serverUrl;

    public static function new()
    {
        return new static();
    }

    public function __construct()
    {
        $this->apiKey    = env('DIFY_API_KEY');
        $this->serverUrl = rtrim(env('DIFY_SERVER_URL'), '/');

        if (empty($this->apiKey) || empty($this->serverUrl)) {
            throw new \Exception('Dify 配置缺失，请检查 .env 中 DIFY_API_KEY / DIFY_SERVER_URL');
        }
    }

    /**
     * 流式发送消息
     */
    public function sendMessageStream($query, callable $callback, $user = null, $conversationId = '', $inputs = [], $files = [])
    {
        $url = sprintf(self::CHAT_MESSAGES_URL, $this->serverUrl);

        $payload = [
            'inputs'          => $inputs,
            'query'           => $query,
            'response_mode'   => 'streaming',
            'conversation_id' => $conversationId ?: '',
            'user'            => $user ?: '',
        ];

        if (!empty($files)) {
            $payload['files'] = $files;
        }

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ];

        // 逐块读取流式输出
        $this->httpPostStream($url, $payload, function ($chunk) use ($callback) {
            $lines = explode("\n", $chunk);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                if (strpos($line, 'data:') === 0) {
                    $json = trim(substr($line, 5));
                    $data = json_decode($json, true);
                    if ($data) $callback($data);
                }
            }
        }, $headers);
    }
}
