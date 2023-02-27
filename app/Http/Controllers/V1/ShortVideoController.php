<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShortVideo;
use App\Services\MediaService;
use App\Services\ShortVideoGoodsService;
use App\Services\ShortVideoPraiseService;
use App\Services\ShortVideoService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MediaTypeEnums;
use App\Utils\Inputs\ShortVideoInput;
use Illuminate\Support\Facades\DB;

class ShortVideoController extends Controller
{
    public function createVideo()
    {
        /** @var ShortVideoInput $input */
        $input = ShortVideoInput::new();

        DB::transaction(function () use ($input) {
            $video = ShortVideoService::getInstance()->newVideo($this->userId(), $input);

            if (!empty($input->goodsId)) {
                ShortVideoGoodsService::getInstance()->newGoods($video->id, $input->goodsId);
            }

            MediaService::getInstance()->newMedia($this->userId(), $video->id, MediaTypeEnums::VIDEO);
        });

        return $this->success();
    }

    public function togglePraiseStatus()
    {
        $id = $this->verifyRequiredId('id');

        /** @var ShortVideo $video */
        $video = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        $praise = ShortVideoPraiseService::getInstance()->getPraise($this->userId(), $id);
        $praiseNumber = DB::transaction(function () use ($video, $id, $praise) {
            if (!is_null($praise)) {
                $praise->delete();
                $praiseNumber = max($video->praise_number - 1, 0);
            } else {
                ShortVideoPraiseService::getInstance()->newPraise($this->userId(), $id);
                $praiseNumber = $video->praise_number + 1;
            }
            $video->praise_number = $praiseNumber;
            $video->save();

            $media = MediaService::getInstance()->getMedia($id, MediaTypeEnums::VIDEO);
            $media->praise_number = $praiseNumber;
            $media->save();

            return $praiseNumber;
        });

        return $this->success($praiseNumber);
    }
}
