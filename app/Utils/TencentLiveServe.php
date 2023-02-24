<?php

namespace App\Utils;

class TencentLiveServe
{
    const PUSH_URL = 'rtmp://push.youboi.com/youboi/ubo_tencent_%s?txSecret=%s&txTime=%s';
    const PLAY_URL = 'rtmp://play.youboi.com/youboi/ubo_tencent_%s';

    public static function new()
    {
        return new static();
    }

    public function getPushUrl($roomId)
    {
        $streamName = 'ubo_tencent_' . $roomId;
        $time = time() + 3600 * 20;
        $key = env('TENCENT_LIVE_ACCESS_KEY');
        $txSecret = md5($key . $streamName . $time);
        return sprintf(self::PUSH_URL, $roomId, $txSecret, $time);
    }

    public function getPlayUrl($roomId)
    {
        return sprintf(self::PLAY_URL, $roomId);
    }
}
