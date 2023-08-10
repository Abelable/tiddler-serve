<?php

namespace App\Services;

use App\Models\Hotel;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\HotelListInput;
use App\Utils\Inputs\AllListInput;

class HotelService extends BaseService
{
    public function getHotelList(HotelListInput $input, $columns=['*'])
    {
        $query = Hotel::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getHotelById($id, $columns=['*'])
    {
        $scenic = Hotel::query()->find($id, $columns);
        if (is_null($scenic)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '景点不存在');
        }
        $scenic->image_list = json_decode($scenic->image_list);
        $scenic->open_time_list = json_decode($scenic->open_time_list);
        $scenic->policy_list = json_decode($scenic->policy_list);
        $scenic->hotline_list = json_decode($scenic->hotline_list);
        $scenic->facility_list = json_decode($scenic->facility_list);
        $scenic->tips_list = json_decode($scenic->tips_list);
        $scenic->project_list = json_decode($scenic->project_list);
        return $scenic;
    }

    public function getHotelListByIds(array $ids, $columns = ['*'])
    {
        return Hotel::query()->whereIn('id', $ids)->get($columns);
    }

    public function getAllList(AllListInput $input, $columns=['*'])
    {
        $query = Hotel::query()->where('status', 1);
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!empty($input->sort)) {
            $query = $query->orderBy($input->sort, $input->order);
        } else {
            $query = $query
                ->orderBy('rate', 'desc')
                ->orderBy('created_at', 'desc');
        }
        return $query->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getHotelOptions($columns = ['*'])
    {
        return Hotel::query()->orderBy('id', 'asc')->get($columns);
    }
}
