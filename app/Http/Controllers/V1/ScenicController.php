<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicSpot;
use App\Services\HotelService;
use App\Services\HotScenicService;
use App\Services\ProductHistoryService;
use App\Services\ScenicEvaluationService;
use App\Services\ScenicQuestionService;
use App\Services\ShopScenicService;
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
    protected $only = ['add', 'edit', 'delete', 'shopOptions'];

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
        $list = ScenicService::getInstance()->handelList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        /** @var CommonPageInput $input */
        $input = CommonPageInput::new();
        $page = ScenicService::getInstance()->search($input);
        $list = ScenicService::getInstance()->handelList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function nearbyList()
    {
        /** @var NearbyPageInput $input */
        $input = NearbyPageInput::new();
        $page = ScenicService::getInstance()->getNearbyPage($input);
        $list = ScenicService::getInstance()->handelList(collect($page->items()));
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
        $list = ScenicService::getInstance()->handelList(collect($page->items()));

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = ScenicService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        $scenic['evaluationSummary'] = ScenicEvaluationService::getInstance()->evaluationSummary($id, 2);
        $scenic['qaSummary'] = ScenicQuestionService::getInstance()->qaSummary($id, 3);
        $scenic['nearbyHotelSummary'] = HotelService::getInstance()
            ->nearbySummary($scenic->longitude, $scenic->latitude, 10);
        $scenic['nearbyScenicSummary'] = ScenicService::getInstance()
            ->nearbySummary($scenic->longitude, $scenic->latitude, 10, $id);

        if ($this->isLogin()) {
            DB::transaction(function () use ($scenic) {
                $scenic->increment('views');
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
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        /** @var ScenicInput $input */
        $input = ScenicInput::new();

        $shopScenicSpot = ShopScenicService::getInstance()->getByScenicId($shopId, $id);
        if (is_null($shopScenicSpot)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无该景点编辑权限');
        }

        $scenic = ScenicService::getInstance()->getScenicById($id);
        ScenicService::getInstance()->updateScenic($scenic, $input);

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopScenicSpot = ShopScenicService::getInstance()->getByScenicId($shopId, $id);
        if (is_null($shopScenicSpot)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '非自家景点，不可删除');
        }

        $restaurant = ScenicService::getInstance()->getScenicById($id);
        $restaurant->delete();

        return $this->success();
    }

    public function shopOptions()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $keywords = $this->verifyString('keywords');

        $scenicIds = ShopScenicService::getInstance()
            ->getShopScenicOptions($shopId)
            ->pluck('scenic_id')
            ->toArray();
        $options = ScenicService::getInstance()->getSelectableOptions($scenicIds, $keywords, ['id', 'name']);

        return $this->success($options);
    }

    public function hotList()
    {
        $list = HotScenicService::getInstance()->getHotScenicList();
        return $this->success($list);
    }
}
