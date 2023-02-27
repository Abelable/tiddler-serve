<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Utils\Inputs\PageInput;

class MediaController extends Controller
{
    public function getMediaList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();


    }
}
