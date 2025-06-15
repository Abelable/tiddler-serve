<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicSpot;
use App\Services\KeywordService;
use App\Services\ProductHistoryService;
use App\Services\ProviderScenicSpotService;
use App\Services\ScenicCategoryService;
use App\Services\ScenicService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\NearbyPageInput;
use App\Utils\Inputs\ScenicInput;
use Illuminate\Support\Facades\DB;

class ScenicController extends Controller
{
    protected $only = ['mediaRelativeList', 'add', 'edit', 'providerOptions'];

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
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();
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

    public function mediaRelativeList()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();

        if ($input->keywords) {
            $page = ScenicService::getInstance()->search($input);
        } else {
            $page = ScenicService::getInstance()->getScenicPage($input);
        }
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
                'featureTagList' => json_decode($spot->feature_tag_list),
                'salesVolume' => $spot->sales_volume
            ];
        });
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = ScenicService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        if ($this->isLogin()) {
            DB::transaction(function () use ($scenic) {
                ScenicService::getInstance()->updateViews($scenic);
                ProductHistoryService::getInstance()
                    ->createHistory($this->userId(), ProductType::SCENIC, $scenic->id);
            });
        }

        return $this->success($scenic);
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

    public function addSales()
    {
        $list = ScenicService::getInstance()->getList();
        /** @var ScenicSpot $scenic */
        foreach ($list as $scenic) {
            if ($scenic->price != 0 && $scenic->sales_volume == 0) {
                $scenic->sales_volume = mt_rand(20, 100);
                $scenic->save();
            }
        }
        return $this->success();
    }
}
