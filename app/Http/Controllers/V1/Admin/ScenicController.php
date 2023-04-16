<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScenicSpot;
use App\Services\ScenicProjectService;
use App\Services\ScenicProviderService;
use App\Services\ScenicService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ScenicListInput;
use App\Utils\Inputs\ScenicAddInput;
use App\Utils\Inputs\ScenicEditInput;
use Illuminate\Support\Facades\DB;

class ScenicController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ScenicListInput $input */
        $input = ScenicListInput::new();
        $columns = [
            'id',
            'status',
            'failure_reason',
            'name',
            'level',
            'category_id',
            'rate',
            'created_at',
            'updated_at'
        ];
        $list = ScenicService::getInstance()->getScenicList($input, $columns);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $scenic = ScenicService::getInstance()->getScenicById($id);
        return $this->success($scenic);
    }

    public function add()
    {
        /** @var ScenicAddInput $input */
        $input = ScenicAddInput::new();

        DB::transaction(function () use ($input) {
            $scenic = ScenicSpot::new();
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
            $scenic->facility_list = json_encode($input->facilityList);
            $scenic->tips_list = json_encode($input->tipsList);
            $scenic->save();

            if (count($input->projectList) != 0) {
                $projectList = array_map(function ($item) use ($scenic) {
                    $item['scenic_id'] = $scenic->id;
                    return $item;
                }, $input->projectList);
                ScenicProjectService::getInstance()->insert($projectList);
            }
        });

        return $this->success();
    }

    public function edit()
    {
        /** @var ScenicEditInput $input */
        $input = ScenicEditInput::new();

        $scenic = ScenicService::getInstance()->getScenicById($input->id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        DB::transaction(function () use ($scenic, $input) {
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
            $scenic->facility_list = json_encode($input->facilityList);
            $scenic->tips_list = json_encode($input->tipsList);

            $scenic->save();

            ScenicProjectService::getInstance()->delete($scenic->id);
            if (count($input->projectList) != 0) {
                $projectList = array_map(function ($item) use ($scenic) {
                    $item['scenic_id'] = $scenic->id;
                    return $item;
                }, $input->projectList);
                ScenicProjectService::getInstance()->insert($projectList);
            }
        });

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = ScenicService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }
        $scenic->delete();

        return $this->success();
    }
}
