<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Utils\Inputs\ShortVideoInput;
use Illuminate\Support\Facades\DB;

class ShortVideoController extends Controller
{
    public function createVideo()
    {
        /** @var ShortVideoInput $input */
        $input = ShortVideoInput::new();

        DB::transaction(function () {

        });
    }
}
