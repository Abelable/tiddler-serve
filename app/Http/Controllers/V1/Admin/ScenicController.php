<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScenicSpot;
use App\Services\ScenicService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ScenicPageInput;
use App\Utils\Inputs\ScenicInput;

class ScenicController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ScenicPageInput $input */
        $input = ScenicPageInput::new();
        $page = ScenicService::getInstance()->getAdminScenicPage($input);
        $list = collect($page->items())->map(function (ScenicSpot $scenic) {
            $scenic['imageList'] = json_decode($scenic->image_list);
            return $scenic;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $scenic = ScenicService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }
        return $this->success($scenic);
    }

    public function add()
    {
        /** @var ScenicInput $input */
        $input = ScenicInput::new();
        $scenic = ScenicSpot::new();
        ScenicService::getInstance()->updateScenic($scenic, $input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var ScenicInput $input */
        $input = ScenicInput::new();

        $scenic = ScenicService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        ScenicService::getInstance()->updateScenic($scenic, $input);

        return $this->success();
    }

    public function editViews()
    {
        $id = $this->verifyRequiredId('id');
        $views = $this->verifyRequiredInteger('views');
        ScenicService::getInstance()->updateViews($id, $views);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = ScenicService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }
        $scenic->delete();

        return $this->success();
    }

    public function options()
    {
        $scenicOptions = ScenicService::getInstance()->getScenicOptions(['id', 'name', 'image_list']);
        $options = $scenicOptions->map(function (ScenicSpot $scenicSpot) {
            return [
                'id' => $scenicSpot->id,
                'name' => $scenicSpot->name,
                'cover' => json_decode($scenicSpot->image_list)[0],
            ];
        });
        return $this->success($options);
    }
}
