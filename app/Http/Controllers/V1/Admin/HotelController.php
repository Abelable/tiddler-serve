<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\HotelService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\HotelListInput;
use App\Utils\Inputs\HotelAddInput;
use App\Utils\Inputs\HotelEditInput;

class HotelController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var HotelListInput $input */
        $input = HotelListInput::new();
        $columns = [
            'id',
            'status',
            'failure_reason',
            'name',
            'grade',
            'category_id',
            'rate',
            'created_at',
            'updated_at'
        ];
        $list = HotelService::getInstance()->getHotelList($input, $columns);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $scenic = HotelService::getInstance()->getHotelById($id);
        return $this->success($scenic);
    }

    public function add()
    {
        /** @var HotelAddInput $input */
        $input = HotelAddInput::new();

        $scenic = Hotel::new();
        $scenic->status = 1;
        $scenic->name = $input->name;
        $scenic->level = $input->level;
        $scenic->category_id = $input->categoryId;
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
        $scenic->save();

        return $this->success();
    }

    public function edit()
    {
        /** @var HotelEditInput $input */
        $input = HotelEditInput::new();

        $scenic = HotelService::getInstance()->getHotelById($input->id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        $scenic->name = $input->name;
        $scenic->level = $input->level;
        $scenic->category_id = $input->categoryId;
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

        $scenic->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = HotelService::getInstance()->getHotelById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }
        $scenic->delete();

        return $this->success();
    }

    public function options()
    {
        $options = HotelService::getInstance()->getHotelOptions(['id', 'name']);
        return $this->success($options);
    }
}
