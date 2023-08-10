<?php

namespace App\Services;

use App\Models\Hotel;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\HotelListInput;
use App\Utils\Inputs\AllListInput;
use App\Utils\Inputs\HotelInput;

class HotelService extends BaseService
{
    public function getHotelList(HotelListInput $input, $columns=['*'])
    {
        $query = Hotel::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->grade)) {
            $query = $query->where('grade', $input->grade);
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

    public function createHotel(HotelInput $input) {
        $hotel = Hotel::new();
        $hotel->status = 1;
        return $this->updateHotel($hotel, $input);
    }

    public function updateHotel(Hotel $hotel, HotelInput $input) {
        $hotel->name = $input->name;
        $hotel->grade = $input->grade;
        $hotel->category_id = $input->categoryId;
        $hotel->price = $input->price;
        if (!empty($input->video)) {
            $hotel->video = $input->video;
        }
        $hotel->image_list = json_encode($input->imageList);
        $hotel->latitude = $input->latitude;
        $hotel->longitude = $input->longitude;
        $hotel->address = $input->address;
        $hotel->feature_tag_list = json_encode($input->featureTagList);
        $hotel->opening_year = $input->openingYear;
        if (!empty($input->lastDecorationYear)) {
            $hotel->last_decoration_year = $input->lastDecorationYear;
        }
        $hotel->room_num = $input->roomNum;
        $hotel->tel = $input->tel;
        if (!empty($input->brief)) {
            $hotel->brief = $input->brief;
        }
        $hotel->recreation_facility = json_encode($input->recreationFacility);
        $hotel->health_facility = json_encode($input->healthFacility);
        $hotel->children_facility = json_encode($input->childrenFacility);
        $hotel->common_facility = json_encode($input->commonFacility);
        $hotel->public_area_facility = json_encode($input->publicAreaFacility);
        $hotel->traffic_service = json_encode($input->trafficService);
        $hotel->catering_service = json_encode($input->cateringService);
        $hotel->reception_service = json_encode($input->receptionService);
        $hotel->clean_service = json_encode($input->cleanService);
        $hotel->business_service = json_encode($input->businessService);
        $hotel->other_service = json_encode($input->otherService);
        $hotel->remind_list = json_encode($input->remindList);
        $hotel->check_in_tip_list = json_encode($input->checkInTipList);
        $hotel->preorder_tip_list = json_encode($input->preorderTipList);
        $hotel->save();

        return $hotel;
    }
}
