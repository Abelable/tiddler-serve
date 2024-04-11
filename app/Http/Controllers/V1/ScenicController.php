<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicSpot;
use App\Services\MallKeywordService;
use App\Services\ProviderScenicSpotService;
use App\Services\ScenicCategoryService;
use App\Services\ScenicService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\NearbyPageInput;
use App\Utils\Inputs\ScenicInput;
use App\Utils\Inputs\SearchPageInput;

class ScenicController extends Controller
{
    protected $only = ['add', 'edit', 'providerOptions'];

    public function categoryOptions()
    {
        $options = ScenicCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();
        $page = ScenicService::getInstance()->getScenicPage($input);
        $list = $this->handelList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();

        if ($this->isLogin()) {
            MallKeywordService::getInstance()->addKeyword($this->userId(), $input->keywords);
        }

        $page = ScenicService::getInstance()->search($input);
        $list = $this->handelList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function nearbyList()
    {
        /** @var NearbyPageInput $input */
        $input = NearbyPageInput::new();
        $page = ScenicService::getInstance()->getNearbyList($input);
        $list = $this->handelList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    private function handelList($scenicList)
    {
        return $scenicList->map(function (ScenicSpot $spot) {
            return [
                'id' => $spot->id,
                'cover' => json_decode($spot->image_list)[0],
                'name' => $spot->name,
                'level' => $spot->level,
                'score' => $spot->score,
                'price' => $spot->price,
                'longitude' => $spot->longitude,
                'latitude' => $spot->latitude,
                'address' => $spot->address,
            ];
        });
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

    public function add()
    {
        /** @var ScenicInput $input */
        $input = ScenicInput::new();

        $scenic = ScenicService::getInstance()->getScenicByName($input->name);
        if (!is_null($scenic)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已存在相同名称景点');
        }

        $scenic = ScenicSpot::new();
        ScenicService::getInstance()->updateScenic($scenic, $input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var ScenicInput $input */
        $input = ScenicInput::new();

        $providerScenicSpot = ProviderScenicSpotService::getInstance()->getSpotByScenicId($this->userId(), $id);
        if (is_null($providerScenicSpot)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无该景点编辑权限');
        }

        $scenic = ScenicService::getInstance()->getScenicById($id);
        ScenicService::getInstance()->updateScenic($scenic, $input);

        return $this->success();
    }

    public function providerOptions()
    {
        $providerScenicIds = ProviderScenicSpotService::getInstance()
            ->getUserScenicOptions($this->userId())
            ->pluck('scenic_id')
            ->toArray();
        $options = ScenicService::getInstance()->getProviderScenicOptions($providerScenicIds, ['id', 'name']);
        return $this->success($options);
    }
}
