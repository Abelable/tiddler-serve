<?php

namespace App\Utils\Enums;

class PromoterScene
{
    const LEVEL_USER = 0;
    const LEVEL_PROMOTER = 1;
    const LEVEL_ORGANIZER_C1 = 2;
    const LEVEL_ORGANIZER_C2 = 3;
    const LEVEL_ORGANIZER_C3 = 4;

    const SCENE_USER = 0;
    const SCENE_PROMOTER = 100;
    const SCENE_ORGANIZER_C1 = 201;
    const SCENE_ORGANIZER_C2 = 202;
    const SCENE_ORGANIZER_C3 = 203;

    const SCENE_TEXT_MAP = [
        self::SCENE_USER => '普通用户',
        self::SCENE_PROMOTER => '代言人Lv.1',
        self::SCENE_ORGANIZER_C1 => '代言人Lv.2',
        self::SCENE_ORGANIZER_C2 => '代言人Lv.3',
        self::SCENE_ORGANIZER_C3 => '代言人Lv.4'
    ];
}
