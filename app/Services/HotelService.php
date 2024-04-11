<?php

namespace App\Services;

use App\Models\Hotel;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\HotelPageInput;
use App\Utils\Inputs\CommonPageInput;
use App\Utils\Inputs\HotelInput;
use App\Utils\Inputs\NearbyPageInput;
use App\Utils\Inputs\SearchPageInput;
use Illuminate\Support\Facades\DB;

class HotelService extends BaseService
{
    public function getAdminHotelPage(HotelPageInput $input, $columns=['*'])
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
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getHotelPage(CommonPageInput $input, $columns=['*'])
    {
        $query = Hotel::query();
        if (!empty($input->keywords)) {
            $query = $query->where('name', 'like', "%$input->keywords%");
        }
        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }
        if (!empty($input->sort)) {
            $query = $query->orderBy($input->sort, $input->order);
        } else {
            $query = $query
                ->orderBy('score', 'desc')
                ->orderBy('created_at', 'desc');
        }
        return $query->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function search(SearchPageInput $input)
    {
        return Hotel::search($input->keywords)
            ->orderBy('score', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, 'page', $input->page);
    }

    public function getNearbyList(NearbyPageInput $input, $columns = ['*'])
    {
        $query = Hotel::query();
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

    public function getHotelById($id, $columns=['*'])
    {
        $hotel = Hotel::query()->find($id, $columns);
        if (is_null($hotel)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '酒店不存在');
        }
        return $this->handleHotelInfo($hotel);
    }

    public function getHotelByName($name, $columns=['*'])
    {
        $hotel = Hotel::query()->where('name', $name)->first($columns);
        if (is_null($hotel)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '酒店不存在');
        }
        return $this->handleHotelInfo($hotel);
    }

    private function handleHotelInfo(Hotel $hotel)
    {
        $hotel->appearance_image_list = json_decode($hotel->appearance_image_list);
        $hotel->interior_image_list = json_decode($hotel->interior_image_list);
        $hotel->room_image_list = json_decode($hotel->room_image_list);
        $hotel->environment_image_list = json_decode($hotel->environment_image_list);
        $hotel->restaurant_image_list = json_decode($hotel->restaurant_image_list);
        $hotel->feature_tag_list = json_decode($hotel->feature_tag_list);
        $hotel->recreation_facility = json_decode($hotel->recreation_facility);
        $hotel->health_facility = json_decode($hotel->health_facility);
        $hotel->children_facility = json_decode($hotel->children_facility);
        $hotel->common_facility = json_decode($hotel->common_facility);
        $hotel->public_area_facility = json_decode($hotel->public_area_facility);
        $hotel->traffic_service = json_decode($hotel->traffic_service);
        $hotel->catering_service = json_decode($hotel->catering_service);
        $hotel->reception_service = json_decode($hotel->reception_service);
        $hotel->clean_service = json_decode($hotel->clean_service);
        $hotel->business_service = json_decode($hotel->business_service);
        $hotel->other_service = json_decode($hotel->other_service);
        $hotel->remind_list = json_decode($hotel->remind_list);
        $hotel->check_in_tip_list = json_decode($hotel->check_in_tip_list);
        $hotel->preorder_tip_list = json_decode($hotel->preorder_tip_list);
        return $hotel;
    }

    public function getHotelListByIds(array $ids, $columns = ['*'])
    {
        return Hotel::query()->whereIn('id', $ids)->get($columns);
    }

    public function getHotelOptions($columns = ['*'])
    {
        return Hotel::query()->orderBy('id', 'asc')->get($columns);
    }

    public function createHotel(HotelInput $input) {
        $hotel = Hotel::new();
        return $this->updateHotel($hotel, $input);
    }

    public function updateHotel(Hotel $hotel, HotelInput $input) {
        $hotel->name = $input->name;
        $hotel->english_name = $input->englishName;
        $hotel->grade = $input->grade;
        $hotel->category_id = $input->categoryId;
        $hotel->price = $input->price;
        if (!empty($input->video)) {
            $hotel->video = $input->video;
        }
        $hotel->cover = $input->cover;
        $hotel->appearance_image_list = json_encode($input->appearanceImageList);
        $hotel->interior_image_list = json_encode($input->interiorImageList);
        $hotel->room_image_list = json_encode($input->roomImageList);
        $hotel->environment_image_list = json_encode($input->environmentImageList);
        $hotel->restaurant_image_list = json_encode($input->restaurantImageList);
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

    public function getProviderHotelOptions(array $hotelIds, $columns = ['*'])
    {
        return Hotel::query()->whereNotIn('id', $hotelIds)->get($columns);
    }

    public function updateHotelAvgScore($hotelId, $avgScore)
    {
        $hotel = $this->getHotelById($hotelId);
        $hotel->score = $avgScore;
        $hotel->save();
        return $hotel;
    }
}
