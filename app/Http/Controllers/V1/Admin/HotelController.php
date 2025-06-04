<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\HotelService;
use App\Utils\Inputs\Admin\HotelPageInput;
use App\Utils\Inputs\HotelInput;

class HotelController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var HotelPageInput $input */
        $input = HotelPageInput::new();
        $columns = [
            'id',
            'cover',
            'name',
            'grade',
            'category_id',
            'price',
            'score',
            'created_at',
            'updated_at'
        ];
        $page = HotelService::getInstance()->getAdminHotelPage($input, $columns);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $hotel = HotelService::getInstance()->getHotelById($id);
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

        HotelService::getInstance()->updateHotel($hotel, $input);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $hotel = HotelService::getInstance()->getHotelById($id);
        $hotel->delete();

        return $this->success();
    }

    public function options()
    {
        $options = HotelService::getInstance()->getHotelOptions(['id', 'name', 'cover']);
        return $this->success($options);
    }
}
