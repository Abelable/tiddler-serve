<?php

namespace App\Utils;

use Mrgoon\AliSms\AliSms;

class AliSmsServe
{
    private $templateCode = [
        'order' => 'SMS_499150104',
    ];

    public static function new()
    {
        return new static();
    }

    public function send($phoneNumber, $template, $data = '')
    {
        try {
            $templateCode = $this->templateCode[$template];
            $aliSms = new AliSms();
            $aliSms->sendSms($phoneNumber, $templateCode, $data);
        } catch (\Exception $exception) {
            throw new \Exception('短信发送异常：' . $exception);
        }

    }
}
