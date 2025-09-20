<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Catering\Restaurant;
use App\Models\Goods;
use App\Models\Hotel;
use App\Models\ScenicSpot;
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
            $pendingBracket = false;
            $stopOutput = false;

            foreach ($stream as $chunk) {
                $text = $chunk['answer'] ?? $chunk['content'] ?? $chunk['text'] ?? '';
                if (!empty($text)) {
                    $fullText .= $text;

                    if (!$stopOutput) {
                        $outputText = $text;

                        // 情况 1：检测 "[R"
                        $pos = strpos(trim($text), '[R');
                        if ($pos !== false) {
                            // 截断 "[R" 之前的部分输出
                            $outputText = substr(trim($text), 0, $pos);
                            echo $outputText;
                            flush();

                            $stopOutput = true;
                            $outputText = '';
                        }

                        // 情况 2：处理可能拆开的 "["
                        if ($pendingBracket) {
                            if (isset(ltrim($text)[0]) && ltrim($text)[0] === 'R') {
                                // 真的是标识开头，不输出
                                $stopOutput = true;
                                $outputText = '';
                            } else {
                                // 不是标识，还原 "["
                                $outputText = '[' . $text;
                            }
                            $pendingBracket = false;
                        } elseif (substr(rtrim($text), -1) === '[') {
                            // 发现可能拆开的标识，先去掉
                            $outputText = substr($text, 0, strrpos($text, '['));
                            $pendingBracket = true;
                        }

                        // 输出干净的内容
                        if ($outputText !== '') {
                            echo $outputText;
                            flush();
                            usleep(50000);
                        }
                    }
                }
            }

            $products = [];
            $pattern = "/\[RECOMMEND_(SPOT|SPOTS|HOTEL|HOTELS|RESTAURANT|RESTAURANTS|PRODUCT|PRODUCTS)(?::([^\]]+))?\]/";

            if (preg_match($pattern, $fullText, $match)) {
                $product_type_map = [
                    'SPOT'=>1,'SPOTS'=>1,'HOTEL'=>2,'HOTELS'=>2,
                    'RESTAURANT'=>3,'RESTAURANTS'=>3,'PRODUCT'=>4,'PRODUCTS'=>4
                ];

                $type = $match[1];
                $name = $match[2] ?? '';
                $product_type = $product_type_map[$type] ?? null;

                if ($product_type) {
                    if (!empty($name)) {
                        // 有明确名称
                        switch ($product_type) {
                            case 1:
                                /** @var ScenicSpot $scenic */
                                $scenic = ScenicService::getInstance()->getScenicByName($name);
                                if ($scenic) {
                                    $scenic = ScenicService::getInstance()->decodeScenicInfo($scenic);
                                    $scenic['cover'] = $scenic->image_list[0];
                                    unset($scenic->image_list);
                                    $products[] = $scenic;
                                }
                                break;

                            case 2:
                                /** @var Hotel $hotel */
                                $hotel = HotelService::getInstance()->getHotelByName($name);
                                if ($hotel) {
                                    $products[] = HotelService::getInstance()->handleHotelInfo($hotel);
                                }
                                break;

                            case 3:
                                /** @var Restaurant $restaurant */
                                $restaurant = RestaurantService::getInstance()->getRestaurantByName($name);
                                if ($restaurant) {
                                    $products[] = RestaurantService::getInstance()->decodeRestaurantInfo($restaurant);
                                }
                                break;

                            case 4:
                                /** @var Goods $goods */
                                $goods = GoodsService::getInstance()->getGoodsByName($name);
                                if ($goods) {
                                    $products[] = GoodsService::getInstance()->decodeGoodsInfo($goods);
                                }
                                break;
                        }
                    } else {
                        // 没有指定名称，取 Top 3
                        switch ($product_type) {
                            case 1:
                                $list = ScenicService::getInstance()->getTopList(3);
                                $products = ScenicService::getInstance()->handleList($list)->toArray();
                                break;

                            case 2:
                                $list = HotelService::getInstance()->getTopList(3);
                                $products = HotelService::getInstance()->handleList($list)->toArray();
                                break;

                            case 3:
                                $list = RestaurantService::getInstance()->getTopList(3);
                                $products = RestaurantService::getInstance()->handleList($list)->toArray();
                                break;

                            case 4:
                                $list = GoodsService::getInstance()->getTopList(3);
                                $products = GoodsService::getInstance()->handleList($list)->toArray();
                                break;
                        }
                    }
                }
            }

            echo "done: " . json_encode(['type' => $product_type ?? 0, 'list' => $products], JSON_UNESCAPED_UNICODE) . "\n\n";
            flush();
        } catch (\Exception $e) {
            echo "error: " . json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE) . "\n\n";
            flush();
            Log::error("AI Chat Stream Error: " . $e->getMessage());
        }

        exit;
    }
}
