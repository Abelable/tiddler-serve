<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Utils\AiServe;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    protected $only = [];

    public function stream()
    {
        $query = $this->verifyRequiredString('query');
        $conversationId = $this->verifyString('conversationId', '');

        // SSE 头
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // 缓冲区设置
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        for ($i = 0; $i < ob_get_level(); $i++) {
            ob_end_flush();
        }
        ob_implicit_flush(true);

        try {
            $ai = AiServe::new();

            // 获取流式生成器
            $stream = $ai->sendMessageStream($query, 'wx_user', $conversationId);

            foreach ($stream as $chunk) {
                if (!empty($chunk)) {
                    echo "data: " . json_encode($chunk, JSON_UNESCAPED_UNICODE) . "\n\n";
                    @ob_flush();
                    flush();
                }
            }

            // 流结束标记
            echo "data: {\"event\":\"stream_end\"}\n\n";
            @ob_flush();
            flush();

        } catch (\Exception $e) {
            echo "event: error\ndata: " . json_encode(['message' => $e->getMessage()]) . "\n\n";
            @ob_flush();
            flush();
            Log::error("AI Chat Stream Error: " . $e->getMessage());
        }

        exit;
    }
}
