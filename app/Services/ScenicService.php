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
        if (!empty($input->productIds)) {
            $query = $query->orderByRaw(DB::raw("FIELD(id, " . implode(',', $input->productIds) . ") DESC"));
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
                ->orderBy('views', 'desc')
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

    public function getNearbyPage(NearbyPageInput $input, $columns = ['*'])
    {
        $query = ScenicSpot::query();
        if (!empty($input->id)) {
            $query = $query->where('id', '!=', $input->id);
        }
        $query = $query
            ->select(
                '*',
                DB::raw(
                    '(6371 * acos(cos(radians(' . $input->latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $input->longitude . ')) + sin(radians(' . $input->latitude . ')) * sin(radians(latitude)))) AS distance'
                )
            );
        if ($input->radius != 0) {
            $query = $query->having('distance', '<=', $input->radius);
        }
        return $query
            ->orderBy('distance')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTopNearbyList($longitude, $latitude, $limit, $scenicId = null, $radius = 10, $columns = ['*'])
    {
        $query = ScenicSpot::query();
        if (!is_null($scenicId)) {
            $query = $query->where('id', '!=', $scenicId);
        }
        $list = $query
            ->select(
                '*',
                DB::raw(
                    '(6371 * acos(cos(radians(' . $latitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $longitude . ')) + sin(radians(' . $latitude . ')) * sin(radians(latitude)))) AS distance'
                )
            )
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->take($limit)
            ->get($columns);
        return $this->handleList($list);
    }

    public function getTopList($count, $columns = ['*'])
    {
        return ScenicSpot::query()
            ->orderBy('views', 'desc')
            ->orderBy('sales_volume', 'desc')
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($count)
            ->get($columns);
    }

    public function handleList($scenicList)
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
                'featureTagList' => json_decode($spot->feature_tag_list) ?? [],
                'salesVolume' => $spot->sales_volume
            ];
        });
    }

    public function nearbySummary($longitude, $latitude, $limit, $scenicId, $radius = 10, $columns = ['*'])
    {
        $list = $this->getTopNearbyList($longitude, $latitude, $limit, $scenicId, $radius, $columns);
        $total = $this->getTotal();
        return ['list' => $list, 'total' => $total - 1];
    }

    public function getScenicById($id, $columns=['*'])
    {
        return ScenicSpot::query()->find($id, $columns);
    }

    public function getScenicByName($name)
    {
        return ScenicSpot::search($name)->first();
    }

    public function decodeScenicInfo(ScenicSpot $scenic)
    {
        $scenic->image_list = json_decode($scenic->image_list);
        $scenic->longitude = (float) $scenic->longitude;
        $scenic->latitude = (float) $scenic->latitude;
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

    public function getSelectableOptions($scenicIds, $keywords = '', $columns = ['*'])
    {
        $query = ScenicSpot::query()->whereNotIn('id', $scenicIds);
        if (!empty($keywords)) {
            $query = $query->where('name', 'like', '%' . $keywords . '%');
        }
        return $query->get($columns);
    }

    public function updateScenicAvgScore($scenicId, $avgScore)
    {
        $scenic = $this->getScenicById($scenicId);
        $scenic->score = $avgScore;
        $scenic->save();
        return $scenic;
    }

    public function updateViews($id, $views)
    {
        return ScenicSpot::query()->where('id', $id)->update(['views' => $views]);
    }

    public function getList()
    {
        return ScenicSpot::query()->get();
    }

    public function getTotal()
    {
        return ScenicSpot::query()->count();
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return ScenicSpot::query()->whereIn('id', $ids)->get($columns);
    }

    public function addSalesVolumeByIds(array $ids, $num)
    {
        ScenicSpot::query()->whereIn('id', $ids)->each(function ($spot) use ($num) {
            $spot->increment('sales_volume', $num);
        });
    }
}
