<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicSpot;
use App\Services\ScenicCategoryService;
use App\Services\ScenicService;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\PageInput;

class ScenicController extends Controller
{
    protected $except = ['categoryOptions', 'list', 'detail', 'options'];

    public function categoryOptions()
    {
        $options = ScenicCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();

        $columns = ['id', 'image_list', 'name', 'level', 'rate', 'longitude', 'latitude', 'address'];
        $page = ScenicService::getInstance()->getScenicPage($input, $columns);
        $scenicList = collect($page->items());
        $list = $scenicList->map(function (ScenicSpot $scenic) {
            $scenic['image'] = json_decode($scenic->image_list)[0];
            unset($scenic->image_list);
            return $scenic;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = ScenicService::getInstance()->search($keywords, $input);
        $list = collect($page->items())->map(function (ScenicSpot $spot) {
            return [
                'id' => $spot->id,
                'cover' => json_decode($spot->image_list)[0],
                'name' => $spot->name,
                'level' => $spot->level,
                'rate' => $spot->rate,
                'longitude' => $spot->longitude,
                'latitude' => $spot->latitude,
                'address' => $spot->address,
            ];
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $scenic = ScenicService::getInstance()->getScenicById($id);
        return $this->success($scenic);
    }

    public function options()
    {
        $scenicOptions = ScenicService::getInstance()->getScenicOptions(['id', 'name']);
        return $this->success($scenicOptions);
    }
}
