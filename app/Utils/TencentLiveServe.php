<?php

namespace App\Utils;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Vod\V20180717\Models\LiveRealTimeClipRequest;
use TencentCloud\Vod\V20180717\VodClient;

class TencentLiveServe
{
    public static function new()
    {
        return new static();
    }

    public function getPushUrl($roomId)
    {
        $streamName = env('TENCENT_LIVE_STREAM_PRE') . $roomId;
        $pushUrl = env('TENCENT_LIVE_PUSH_PRE') . $streamName . '?txSecret=%s&txTime=%s';
        $key = env('TENCENT_LIVE_ACCESS_KEY');
        $time = time() + 3600 * 20;
        $txSecret = md5($key . $streamName . $time);
        return sprintf($pushUrl, $txSecret, $time);
    }

    public function getPlayUrl($roomId)
    {
        return env('TENCENT_LIVE_PLAY_PRE') . env('TENCENT_LIVE_STREAM_PRE') . $roomId;;
    }

    /**
     * 生成直播回放
     * @param $roomId
     * @param $startTime
     * @param $endTime
     * @return mixed
     * @throws \Exception
     */
    public function liveRealTimeClip($roomId, $startTime, $endTime)
    {
        try {
            $cred = new Credential(env('TENCENT_LIVE_SECRET_ID'), env('TENCENT_LIVE_SECRET_KEY'));
            $httpProfile = new HttpProfile();
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, 'ap-chongqing', $clientProfile);
            $request = new LiveRealTimeClipRequest();
            $request->StreamId = env('TENCENT_LIVE_STREAM_PRE') . $roomId;
            $request->StartTime = $this->utcToIso($startTime);
            $request->EndTime = $this->utcToIso($endTime);
            $request->Host = env('TENCENT_LIVE_VIDEO_HOST');
            $res = $client->LiveRealTimeClip($request);
            return $res['Url'];
        } catch (TencentCloudSDKException $ex) {
            throw new \Exception('直播回放生成失败', $ex, 312);
        }
    }

    /**
     * 转换请求格式时间戳
     * @param $date // int 类型时间
     * @return string 腾讯云ISO 格式时间戳
     */
    private function utcToIso($date)
    {
        $date = date(DATE_ISO8601, $date - (8 * 60 * 60));
        $arr = explode("+", $date);
        return $arr[0] . 'Z';
    }
}
