<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\PromoterQa;
use App\Services\PromoterQaService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class PromoterQaController extends Controller
{
    protected $except = ['summary', 'list'];

    public function summary()
    {
        $promoterId = $this->verifyRequiredId('promoterId');

        $answerCount = PromoterQaService::getInstance()->getAnswerCount($promoterId);
        $averageDuration = PromoterQaService::getInstance()->getAnswerAverageDuration($promoterId);

        return $this->success([
            'answerCount' => $answerCount ?? 0,
            'averageDuration' => (int)$averageDuration ?? 0,
        ]);
    }

    public function list()
    {
        $promoterId = $this->verifyRequiredId('promoterId');
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = PromoterQaService::getInstance()->getQaPage($promoterId, $input);
        $qaList = collect($page->items());

        $userIds = $qaList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $qaList->map(function (PromoterQa $qa) use ($userList) {
            $userInfo = $userList->get($qa->user_id);
            $qa['userInfo'] = $userInfo;
            unset($qa->user_id);
            return $qa;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function add()
    {
        $promoterId = $this->verifyRequiredId('promoterId');
        $content = $this->verifyRequiredString('content');

        PromoterQaService::getInstance()->createQa($this->userId(), $promoterId, $content);

        return $this->success();
    }

    public function answer()
    {
        $id = $this->verifyRequiredId('id');
        $content = $this->verifyRequiredString('content');

        $qa = PromoterQaService::getInstance()->getPromoterQa($this->userId(), $id);
        if (is_null($qa)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提问不存在');
        }

        $qa->answer = $content;
        $qa->answer_time = now()->toDateTimeString();
        $qa->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $qa = PromoterQaService::getInstance()->getUserQa($this->userId(), $id);
        if (is_null($qa)) {
            return $this->fail(CodeResponse::NOT_FOUND, '非本人提问，无非删除');
        }
        $qa->delete();

        return $this->success();
    }
}
