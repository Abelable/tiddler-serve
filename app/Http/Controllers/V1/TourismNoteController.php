<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Media\Note\TourismNoteService;
use App\Utils\Inputs\PageInput;

class TourismNoteController extends Controller
{
    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyRequiredId('id');

        $page = TourismNoteService::getInstance()->pageList($id, $input);

    }
}
