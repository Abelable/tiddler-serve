<?php

namespace App\Http\Controllers\V1;

use App\Services\Activity\NewYearTaskService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CallbackController extends Controller
{
    public function handleGroupChange(Request $request)
    {
        if ($request->has('echostr')) {
            return response($request->get('echostr'));
        }

        $data = $request->all();
        $changeType = $data['ChangeType'] ?? null;
        $chatId = $data['ChatId'] ?? null;
        $externalUserId = $data['ExternalUserId'] ?? null;
        $scene = $data['Scene'] ?? null;

        if (!$changeType || !$chatId) {
            return response()->json(['errcode' => 400, 'errmsg' => '必填字段缺失']);
        }

        $userId = null;
        if ($scene && str_starts_with($scene, 'user_')) {
            $userId = intval(substr($scene, 5));
        }

        switch ($changeType) {
            case 'add_member':
                if ($userId) {
                    // todo 团圆家乡年
                    NewYearTaskService::getInstance()->finishTask($userId, 4);
                }
                break;

            case 'del_member':
                if ($userId) {
                    // 退群处理逻辑
                }
                break;

            default:
                return response()->json(['errcode' => 400, 'errmsg' => '未知事件类型']);
        }

        return response()->json(['errcode' => 0, 'errmsg' => 'ok']);
    }
}
