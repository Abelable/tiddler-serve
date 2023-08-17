<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelRoomType;
use App\Services\HotelRoomTypeService;
use App\Utils\Inputs\HotelRoomTypeInput;
use App\Utils\Inputs\PageInput;

class HotelRoomTypeController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        $hotelId = $this->verifyRequiredId('hotelId');
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = HotelRoomTypeService::getInstance()->getTypeList($hotelId, $input);
        $typeList = collect($page->items());
        $list = $typeList->map(function (HotelRoomType $hotelRoomType) {
            $hotelRoomType->image_list = json_encode($hotelRoomType->image_list);
            return $hotelRoomType;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $hotel = HotelRoomTypeService::getInstance()->getTypeById($id);
        return $this->success($hotel);
    }

    public function add()
    {
        /** @var HotelRoomTypeInput $input */
        $input = HotelRoomTypeInput::new();
        HotelRoomTypeService::getInstance()->createType($input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var HotelRoomTypeInput $input */
        $input = HotelRoomTypeInput::new();

        $type = HotelRoomTypeService::getInstance()->getTypeById($id);

        HotelRoomTypeService::getInstance()->updateType($type, $input);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $type = HotelRoomTypeService::getInstance()->getTypeById($id);
        $type->delete();

        return $this->success();
    }

    public function options()
    {
        $options = HotelRoomTypeService::getInstance()->getTypeOptions(['id', 'name']);
        return $this->success($options);
    }
}
