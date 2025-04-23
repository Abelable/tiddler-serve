<?php

namespace App\Services;

use App\Models\ScenicSpot;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ScenicPageInput;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\NearbyPageInput;
use App\Utils\Inputs\ScenicInput;
use Illuminate\Support\Facades\DB;

class ScenicService extends BaseService
{
    public function getAdminScenicPage(ScenicPageInput $input, $columns=['*'])
    {
        $query = ScenicSpot::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getScenicPage(CommonPageInput $input, $columns = ['*'])
    {
        $query = ScenicSpot::query();
        if (!empty($input->commodityIds)) {
            $query = $query->orderByRaw(DB::raw("FIELD(id, " . implode(',', $input->commodityIds) . ") DESC"));
        }
        if (!empty($input->keywords)) {
            $query = $query->where('name', 'like', "%$input->keywords%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if ($input->sort != 'created_at') {
            $query = $query->orderBy($input->sort, $input->order);
        } else {
            $query = $query
                ->orderBy('sales_volume', 'desc')
                ->orderBy('score', 'desc')
                ->orderBy('created_at', 'desc');
        }
        return $query->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function search(CommonPageInput $input)
    {
        return ScenicSpot::search($input->keywords)
            ->orderBy('score', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, 'page', $input->page);
    }

    public function getNearbyList(NearbyPageInput $input, $columns = ['*'])
    {
        $query = ScenicSpot::query();
        if (!empty($input->id)) {
            $query = $query->where('id', '!=', $input->id);
        }
        return $query
            ->select(
                '*',
                DB::raw(
                    '(6371 * acos(cos(radians(' . $input->latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $input->longitude . ')) + sin(radians(' . $input->latitude . ')) * sin(radians(latitude)))) AS distance'
                )
            )
            ->having('distance', '<=', $input->radius)
            ->orderBy('distance')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getScenicById($id, $columns=['*'])
    {
        $scenic = ScenicSpot::query()->find($id, $columns);
        if (is_null($scenic)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '景点不存在');
        }
        return $this->decodeScenicInfo($scenic);
    }

    public function getUserScenic($userId, $scenicId, $columns = ['*'])
    {
        $scenic = ScenicSpot::query()->where('user_id', $userId)->find($scenicId, $columns);
        if (is_null($scenic)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '景点不存在');
        }
        return $this->decodeScenicInfo($scenic);
    }

    public function getScenicByName($name, $columns = ['*'])
    {
        $scenic = ScenicSpot::query()->where('name', $name)->first($columns);
        if (is_null($scenic)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '景点不存在');
        }
        return $this->decodeScenicInfo($scenic);
    }

    private function decodeScenicInfo(ScenicSpot $scenic)
    {
        $scenic->longitude = (float) $scenic->longitude;
        $scenic->latitude = (float) $scenic->latitude;
        $scenic->image_list = json_decode($scenic->image_list);
        $scenic->open_time_list = json_decode($scenic->open_time_list);
        $scenic->policy_list = json_decode($scenic->policy_list);
        $scenic->hotline_list = json_decode($scenic->hotline_list);
        $scenic->facility_list = json_decode($scenic->facility_list);
        $scenic->tips_list = json_decode($scenic->tips_list);
        $scenic->project_list = json_decode($scenic->project_list);
        $scenic->feature_tag_list = json_decode($scenic->feature_tag_list);
        return $scenic;
    }

    public function getScenicListByIds(array $ids, $columns = ['*'])
    {
        return ScenicSpot::query()->whereIn('id', $ids)->get($columns);
    }

    public function getScenicOptions($columns = ['*'])
    {
        return ScenicSpot::query()->orderBy('id', 'asc')->get($columns);
    }

    public function updateScenic(ScenicSpot $scenic, ScenicInput $input)
    {
        $scenic->name = $input->name;
        if (!empty($input->level)) {
            $scenic->level = $input->level;
        }
        $scenic->category_id = $input->categoryId;
        $scenic->price = $input->price;
        if (!empty($input->video)) {
            $scenic->video = $input->video;
        }
        $scenic->image_list = json_encode($input->imageList);
        $scenic->latitude = $input->latitude;
        $scenic->longitude = $input->longitude;
        $scenic->address = $input->address;
        $scenic->brief = $input->brief;
        $scenic->open_time_list = json_encode($input->openTimeList);
        $scenic->policy_list = json_encode($input->policyList);
        $scenic->hotline_list = json_encode($input->hotlineList);
        $scenic->project_list = json_encode($input->projectList);
        $scenic->facility_list = json_encode($input->facilityList);
        $scenic->tips_list = json_encode($input->tipsList);
        $scenic->feature_tag_list = json_encode($input->featureTagList);
        $scenic->save();

        return $scenic;
    }

    public function getProviderScenicOptions($scenicIds, $columns = ['*'])
    {
        return ScenicSpot::query()->whereNotIn('id', $scenicIds)->get($columns);
    }

    public function updateScenicAvgScore($scenicId, $avgScore)
    {
        $scenic = $this->getScenicById($scenicId);
        $scenic->score = $avgScore;
        $scenic->save();
        return $scenic;
    }

    public function getList()
    {
        return ScenicSpot::query()->get();
    }

    public function addSalesVolumeByIds(Array $ids, $num)
    {
        ScenicSpot::query()->whereIn('id', $ids)->each(function ($spot) use ($num) {
            $spot->increment('sales_volume', $num);
        });
    }
}
