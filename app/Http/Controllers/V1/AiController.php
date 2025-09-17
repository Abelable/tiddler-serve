<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Utils\AiServe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    protected $only = [];

    public function stream()
    {
        $query = $this->verifyRequiredString('query');
        $conversationId = $this->verifyString('conversationId', '');

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if (function_exists('ob_implicit_flush')) {
            ob_implicit_flush(true);
        }
        while (@ob_end_flush());

        try {
            $ai = AiServe::new();

            $ai->sendMessageStream($query, function ($chunk) {
                if ($chunk) {
                    echo "data: " . json_encode($chunk, JSON_UNESCAPED_UNICODE) . "\n\n";
                    @ob_flush();
                    flush();
                }
            }, 'wx_user', $conversationId);

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
    }
}
