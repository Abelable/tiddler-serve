<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GoodsService;
use App\Services\HotelService;
use App\Services\RestaurantService;
use App\Services\ScenicService;
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
            $stream = $ai->sendMessageStream($query, $this->userId() ?: 'visitor', $conversationId);

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

    public function mpStream()
    {
        $query = $this->verifyRequiredString('query');
        $conversationId = $this->verifyString('conversationId', '');

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if (ob_get_level()) ob_end_clean();
        ob_implicit_flush(true);

        try {
            $ai = AiServe::new();
            $stream = $ai->sendMessageStream($query, $this->userId() ?: 'visitor', $conversationId);

            $fullText = '';

            foreach ($stream as $chunk) {
                $text = $chunk['answer'] ?? $chunk['content'] ?? $chunk['text'] ?? '';
                if (!empty($text)) {
                    $fullText .= $text;

                    echo $text;
                    flush();

                    usleep(50000);
                }
            }

            $products = [];
            $pattern = "/\[RECOMMEND_(SPOT|SPOTS|HOTEL|HOTELS|RESTAURANT|RESTAURANTS|PRODUCT|PRODUCTS)(?::([^\]]+))?\]/";
            if (preg_match_all($pattern, $fullText, $matches, PREG_SET_ORDER)) {
                $product_type_map = [
                    'SPOT'=>1,'SPOTS'=>1,'HOTEL'=>2,'HOTELS'=>2,
                    'RESTAURANT'=>3,'RESTAURANTS'=>3,'PRODUCT'=>4,'PRODUCTS'=>4
                ];

                foreach ($matches as $match) {
                    $type = $match[1];
                    $name = $match[2] ?? '';
                    $product_type = $product_type_map[$type] ?? null;
                    if (!$product_type) continue;

                    if (!empty($name)) {
                        switch ($product_type) {
                            case 1:
                                $product = ScenicService::getInstance()->getScenicByName($name);
                                if ($product) {
                                    $product = ScenicService::getInstance()->decodeScenicInfo($product);
                                    $product['cover'] = $product->image_list[0];
                                    unset($product->image_list);
                                }
                                break;

                            case 2:
                                $product = HotelService::getInstance()->getHotelByName($name);
                                if ($product) {
                                    $product = HotelService::getInstance()->handleHotelInfo($product);
                                }
                                break;

                            case 3:
                                $product = RestaurantService::getInstance()->getRestaurantByName($name);
                                if ($product) {
                                    $product = RestaurantService::getInstance()->decodeRestaurantInfo($product);
                                }
                                break;

                            case 4:
                                $product = GoodsService::getInstance()->getGoodsByName($name);
                                if ($product) {
                                    $product = GoodsService::getInstance()->decodeGoodsInfo($product);
                                }
                                break;
                        }
                        if ($product) $products[] = $product;
                    } else {
                        switch ($product_type) {
                            case 1:
                                $list = ScenicService::getInstance()->getTopList(3);
                                $list = ScenicService::getInstance()->handleList($list);
                                break;

                            case 2:
                                $list = HotelService::getInstance()->getTopList(3);
                                $list = HotelService::getInstance()->handleList($list);
                                break;

                            case 3:
                                $list = RestaurantService::getInstance()->getTopList(3);
                                $list = RestaurantService::getInstance()->handleList($list);
                                break;

                            case 4:
                                $list = GoodsService::getInstance()->getTopList(3);
                                $list = GoodsService::getInstance()->handleList($list);
                                break;
                        }
                        $products = array_merge($products, $list->toArray());
                    }
                }
            }

            echo json_encode(['type' => $product_type, 'list' => $products], JSON_UNESCAPED_UNICODE) . "\n\n";
            flush();

        } catch (\Exception $e) {
            echo "event: error\n";
            echo "data: " . json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE) . "\n\n";
            flush();
            Log::error("AI Chat Stream Error: " . $e->getMessage());
        }

        exit;
    }
}
