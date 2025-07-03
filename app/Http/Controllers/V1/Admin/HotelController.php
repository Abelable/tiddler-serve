<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\HotelService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\HotelPageInput;
use App\Utils\Inputs\HotelInput;

class HotelController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var HotelPageInput $input */
        $input = HotelPageInput::new();
        $page = HotelService::getInstance()->getAdminHotelPage($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $hotel = HotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店不存在');
        }

        return $this->success($hotel);
    }

    public function add()
    {
        /** @var HotelInput $input */
        $input = HotelInput::new();
        HotelService::getInstance()->createHotel($input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var HotelInput $input */
        $input = HotelInput::new();

        $hotel = HotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店不存在');
        }

        HotelService::getInstance()->updateHotel($hotel, $input);

        return $this->success();
    }

    public function editViews()
    {
        $id = $this->verifyRequiredId('id');
        $views = $this->verifyRequiredInteger('views');
        HotelService::getInstance()->updateViews($id, $views);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $hotel = HotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店不存在');
        }
        $hotel->delete();

        return $this->success();
    }

    public function options()
    {
        $options = HotelService::getInstance()->getHotelOptions(['id', 'name', 'cover']);
        return $this->success($options);
    }
}
