<?php

namespace App\Utils;

use App\Utils\Traits\HttpClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WechatTransferServe
{
    use HttpClient;

    protected $mchid;
    protected $serialNo;
    protected $privateKeyPath;
    protected $appid;

    public static function new() { return new static(); }

    public function __construct()
    {
        $this->mchid = env('WX_PAY_MCH_ID');
        $this->serialNo = env('WX_PAY_CERT_SERIAL');
        $this->appid = env('WX_MP_APPID');
        $this->privateKeyPath = storage_path('app/wxpay/apiclient_key.pem');
    }

    public function transferToUser($outBillNo, $openid, $amount, $remark, $title)
    {
        $urlPath = '/v3/fund-app/mch-transfer/transfer-bills';
        $fullUrl = 'https://api.mch.weixin.qq.com' . $urlPath;
        $timestamp = time();
        $nonce = Str::random(32);

        $data = [
            'appid'             => $this->appid,
            'out_bill_no'       => $outBillNo,
            'transfer_scene_id' => '1005',
            'openid'            => $openid,
            'transfer_amount'   => (int) bcmul($amount, 100, 0),
            'transfer_remark'   => $remark,
            'transfer_scene_report_infos' => [
                ['info_type' => '岗位类型', 'info_content' => $title],
                ['info_type' => '报酬说明', 'info_content' => $remark],
            ],
        ];

        // 关键点1：JSON 必须禁用转义
        $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // 关键点2：生成签名
        $signature = $this->makeV3Signature('POST', $urlPath, $timestamp, $nonce, $body);

        $authorization = sprintf(
            'WECHATPAY2-SHA256-RSA2048 mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $this->mchid, $nonce, $timestamp, $this->serialNo, $signature
        );

        // 关键点3：使用 withBody 发送原始字符串，避免 Laravel Http 再次转换格式
        $response = Http::withHeaders([
            'Authorization' => $authorization,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'User-Agent'    => 'Laravel/WechatPayV3',
        ])->withBody($body, 'application/json')->post($fullUrl);

        if (!$response->successful()) {
            $err = $response->json();
            Log::error('微信转账明细错误', ['http_status' => $response->status(), 'response' => $err]);
            throw new \Exception("微信转账失败：[" . ($err['code'] ?? 'ERR') . "] " . ($err['message'] ?? '未知错误'));
        }

        return $response->json();
    }

    private function makeV3Signature($method, $url, $timestamp, $nonce, $body)
    {
        // 关键点4：待签名字符串格式，最后一行必须带换行符 \n
        $message = $method . "\n" .
            $url . "\n" .
            $timestamp . "\n" .
            $nonce . "\n" .
            $body . "\n";

        if (!file_exists($this->privateKeyPath)) {
            throw new \Exception("私钥文件不存在");
        }

        $privateKey = file_get_contents($this->privateKeyPath);
        $keyResource = openssl_get_privatekey($privateKey);

        if (!$keyResource) {
            throw new \Exception("私钥解析失败，请检查 pem 格式是否正确");
        }

        openssl_sign($message, $rawSignature, $keyResource, 'sha256WithRSAEncryption');
        openssl_free_key($keyResource);

        return base64_encode($rawSignature);
    }
}
