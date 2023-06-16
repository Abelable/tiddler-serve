<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicSpot;
use App\Services\ScenicCategoryService;
use App\Services\ScenicService;
use App\Utils\Inputs\AllListInput;

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
        /** @var AllListInput $input */
        $input = AllListInput::new();

        $columns = ['id', 'image_list', 'name', 'level', 'rate', 'longitude', 'latitude', 'address'];
        $page = ScenicService::getInstance()->getAllList($input, $columns);
        $scenicList = collect($page->items());
        $list = $scenicList->map(function (ScenicSpot $scenic) {
            $scenic['image'] = json_decode($scenic->image_list)[0];
            unset($scenic->image_list);
            return $scenic;
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
