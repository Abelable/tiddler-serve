<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity\NewYearPrize;
use App\Services\Activity\NewYearPrizeService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Activity\NewYearPrizeInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;

class NewYearPrizeController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = NewYearPrizeService::getInstance()->getPage($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $prize = NewYearPrizeService::getInstance()->getPrizeById($id);
        if (is_null($prize)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前奖品不存在');
        }
        return $this->success($prize);
    }

    public function add()
    {
        /** @var NewYearPrizeInput $input */
        $input = NewYearPrizeInput::new();

        $prize = NewYearPrize::new();
        NewYearPrizeService::getInstance()->updatePrize($prize, $input);

        Cache::forget('new_year_prize_list');

        return $this->success();
    }

    public function edit()
    {
        /** @var NewYearPrizeInput $input */
        $input = NewYearPrizeInput::new();
        $id = $this->verifyRequiredId('id');

        $prize = NewYearPrizeService::getInstance()->getPrizeById($id);
        if (is_null($prize)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前奖品不存在');
        }

        NewYearPrizeService::getInstance()->updatePrize($prize, $input);

        Cache::forget('new_year_prize_list');

        return $this->success();
    }

    public function editIsBig()
    {
        $id = $this->verifyRequiredId('id');
        $isBig = $this->verifyRequiredInteger('isBig');

        $prize = NewYearPrizeService::getInstance()->getPrizeById($id);
        if (is_null($prize)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前奖品不存在');
        }

        NewYearPrizeService::getInstance()->updateIsBig($id, $isBig);

        Cache::forget('new_year_prize_list');

        return $this->success();
    }

    public function editSort()
    {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $prize = NewYearPrizeService::getInstance()->getPrizeById($id);
        if (is_null($prize)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前奖品不存在');
        }

        NewYearPrizeService::getInstance()->updateSort($id, $sort);

        Cache::forget('new_year_prize_list');

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $prize = NewYearPrizeService::getInstance()->getPrizeById($id);
        if (is_null($prize)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前奖品不存在');
        }

        $prize->status = 1;
        $prize->save();

        Cache::forget('new_year_prize_list');

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');

        $prize = NewYearPrizeService::getInstance()->getPrizeById($id);
        if (is_null($prize)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前奖品不存在');
        }

        $prize->status = 2;
        $prize->save();

        Cache::forget('new_year_prize_list');

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $prize = NewYearPrizeService::getInstance()->getPrizeById($id);
        if (is_null($prize)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前奖品不存在');
        }
        $prize->delete();

        Cache::forget('new_year_prize_list');

        return $this->success();
    }
}
