<?php

namespace App\Services\Mall\Hotel;

use App\Models\Mall\Hotel\HotelRoomType;
use App\Services\BaseService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\HotelRoomTypeInput;
use App\Utils\Inputs\PageInput;

class HotelRoomTypeService extends BaseService
{
    public function getTypeList($hotelId, pageInput $input, $columns=['*'])
    {
        return HotelRoomType::query()
            ->where('hotel_id', $hotelId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return HotelRoomType::query()->whereIn('id', $ids)->get($columns);
    }

    public function getTypeById($id, $columns=['*'], $needDecode = true)
    {
        /** @var HotelRoomType $type */
        $type = HotelRoomType::query()->find($id, $columns);
        if (is_null($type)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '房型不存在');
        }
        if ($needDecode) {
            $type->image_list = json_decode($type->image_list);
            $type->facility_list = json_decode($type->facility_list);
        }
        return $type;
    }

    public function getTypeOptions($hotelId, $columns = ['*'])
    {
        return HotelRoomType::query()->where('hotel_id', $hotelId)->orderBy('id', 'asc')->get($columns);
    }

    public function createType(HotelRoomTypeInput $input) {
        $type = HotelRoomType::new();
        $type->hotel_id = $input->hotelId;
        return $this->updateType($type, $input);
    }

    public function updateType(HotelRoomType $type, HotelRoomTypeInput $input) {
        $type->hotel_id = $input->hotelId;
        $type->name = $input->name;
        $type->image_list = json_encode($input->imageList);
        $type->bed_desc = $input->bedDesc;
        $type->area_size = $input->areaSize;
        $type->floor_desc = $input->floorDesc;
        $type->facility_list = json_encode($input->facilityList);
        $type->save();

        return $type;
    }
}
