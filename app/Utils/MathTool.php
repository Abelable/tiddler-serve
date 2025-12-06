<?php

namespace App\Utils;

class MathTool
{
    public static function bcRound($amount, int $scale = 2) {
        $factor = bcpow('10', (string)$scale);
        $tmp = bcmul((string)$amount, $factor, 10);
        $tmp = bcadd($tmp, '0.5', 0);
        return bcdiv($tmp, $factor, $scale);
    }
}
