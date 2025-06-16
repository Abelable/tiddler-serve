<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaProduct;
use App\Models\TourismNote;
use App\Services\Media\Note\TourismNoteCollectionService;
use App\Services\Media\Note\TourismNoteCommentService;
use App\Services\Media\Note\TourismNoteLikeService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\MediaProductService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MediaType;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\Admin\MediaPageInput;
use App\Utils\Inputs\TourismNoteInput;
use Illuminate\Support\Facades\DB;

class TourismNoteController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MediaPageInput $input */
        $input = MediaPageInput::new();

        $page = TourismNoteService::getInstance()->adminPage($input);
        $tourismNoteList = collect($page->items());
        $tourismNoteIds = $tourismNoteList->pluck('id')->toArray();

        $relatedProductList = MediaProductService::getInstance()
            ->getListByMediaIds(MediaType::NOTE, $tourismNoteIds)->groupBy('media_id');

        $list = $tourismNoteList->map(function (TourismNote $tourismNote) use ($relatedProductList) {
            $scenicIds = $hotelIds = $restaurantIds = $goodsIds = [];

            $productList = $relatedProductList->get($tourismNote->id);
            if (!empty($productList)) {
                /** @var MediaProduct $mediaProduct */
                foreach ($productList as $mediaProduct) {
                    switch ($mediaProduct->product_type) {
                        case ProductType::SCENIC:
                            $scenicIds[] = $mediaProduct->product_id;
                            break;
                        case ProductType::HOTEL:
                            $hotelIds[] = $mediaProduct->product_id;
                            break;
                        case ProductType::RESTAURANT:
                            $restaurantIds[] = $mediaProduct->product_id;
                            break;
                        case ProductType::GOODS:
                            $goodsIds[] = $mediaProduct->product_id;
                            break;
                    }
                }
            }

            $tourismNote['scenicIds'] = $scenicIds;
            $tourismNote['hotelIds'] = $hotelIds;
            $tourismNote['restaurantIds'] = $restaurantIds;
            $tourismNote['goodsIds'] = $goodsIds;

            $tourismNote['imageList'] = json_decode($tourismNote['image_list'], true);

            return $tourismNote;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $tourismNote = TourismNoteService::getInstance()->getNote($id);
        if (is_null($tourismNote)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前视频游记不存在');
        }

        $relatedProductList = MediaProductService::getInstance()->getListByMediaIds(MediaType::NOTE, [$id]);
        $scenicIds = $hotelIds = $restaurantIds = $goodsIds = [];
        foreach ($relatedProductList as $mediaProduct) {
            switch ($mediaProduct->product_type) {
                case ProductType::SCENIC:
                    $scenicIds[] = $mediaProduct->product_id;
                    break;
                case ProductType::HOTEL:
                    $hotelIds[] = $mediaProduct->product_id;
                    break;
                case ProductType::RESTAURANT:
                    $restaurantIds[] = $mediaProduct->product_id;
                    break;
                case ProductType::GOODS:
                    $goodsIds[] = $mediaProduct->product_id;
                    break;
            }
        }
        $tourismNote['scenicIds'] = $scenicIds;
        $tourismNote['hotelIds'] = $hotelIds;
        $tourismNote['restaurantIds'] = $restaurantIds;
        $tourismNote['goodsIds'] = $goodsIds;

        $tourismNote['imageList'] = json_decode($tourismNote['image_list'], true);

        return $this->success($tourismNote);
    }

    public function add()
    {
        $userId = $this->verifyRequiredId('userId');
        /** @var TourismNoteInput $input */
        $input = TourismNoteInput::new();

        DB::transaction(function () use ($userId, $input) {
            $note = TourismNoteService::getInstance()->createNote($userId, $input);

            foreach ($input->scenicIds as $scenicId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::SCENIC,
                    $scenicId,
                );
            }

            foreach ($input->hotelIds as $hotelId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::HOTEL,
                    $hotelId,
                );
            }

            foreach ($input->restaurantIds as $restaurantId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::RESTAURANT,
                    $restaurantId,
                );
            }

            foreach ($input->goodsIds as $goodsId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::GOODS,
                    $goodsId,
                );
            }
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $userId = $this->verifyRequiredId('userId');
        /** @var TourismNoteInput $input */
        $input = TourismNoteInput::new();

        $note = TourismNoteService::getInstance()->getNote($id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前视频游记不存在');
        }

        DB::transaction(function () use ($userId, $input, $note) {
            TourismNoteService::getInstance()->updateNote($note, $userId, $input);

            MediaProductService::getInstance()->deleteList(MediaType::NOTE, $note->id);

            foreach ($input->scenicIds as $scenicId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::SCENIC,
                    $scenicId,
                );
            }

            foreach ($input->hotelIds as $hotelId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::HOTEL,
                    $hotelId,
                );
            }

            foreach ($input->restaurantIds as $restaurantId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::RESTAURANT,
                    $restaurantId,
                );
            }

            foreach ($input->goodsIds as $goodsId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::GOODS,
                    $goodsId,
                );
            }
        });

        return $this->success();
    }

    public function editViews()
    {
        $id = $this->verifyRequiredId('id');
        $views = $this->verifyRequiredInteger('views');

        $tourismNote = TourismNoteService::getInstance()->getNote($id);
        if (is_null($tourismNote)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前视频游记不存在');
        }

        $tourismNote->views = $views;
        $tourismNote->save();

        return $this->success();
    }

    public function options()
    {
        $list = TourismNoteService::getInstance()->getList(['id', 'image_list', 'title']);
        $options = $list->map(function (TourismNote $note) {
            $note['cover'] = json_decode($note->image_list, true)[0];
            unset($note->image_list);

            return $note;
        });

        return $this->success($options);
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $tourismNote = TourismNoteService::getInstance()->getNote($id);
        if (is_null($tourismNote)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前视频游记不存在');
        }

        DB::transaction(function () use ($tourismNote) {
            $tourismNote->delete();
            MediaProductService::getInstance()->deleteList(MediaType::NOTE, $tourismNote->id);
            TourismNoteCollectionService::getInstance()->deleteList($tourismNote->id);
            TourismNoteCommentService::getInstance()->deleteList($tourismNote->id);
            TourismNoteLikeService::getInstance()->deleteList($tourismNote->id);
        });

        return $this->success();
    }
}
