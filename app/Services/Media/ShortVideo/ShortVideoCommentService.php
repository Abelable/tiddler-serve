<?php

namespace App\Services\Media\ShortVideo;

use App\Models\ShortVideoComment;
use App\Services\BaseService;
use App\Utils\Inputs\CommentInput;
use App\Utils\Inputs\CommentListInput;
use Illuminate\Support\Facades\DB;

class ShortVideoCommentService extends BaseService
{
    public function pageList(CommentListInput $input, $columns = ['*'])
    {
        $query = ShortVideoComment::query();
        return $query
            ->where('parent_id', $input->commentId ?? 0)
            ->with('userInfo')
            ->where('video_id', $input->mediaId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function newComment($userId, CommentInput $input)
    {
        $comment = ShortVideoComment::new();
        $comment->user_id = $userId;
        $comment->video_id = $input->mediaId;
        $comment->content = $input->content;
        if (!empty($input->commentId)) {
            $comment->parent_id = $input->commentId;
        }
        $comment->save();
        return $comment;
    }

    public function getComment($userId, $id, $columns = ['*'])
    {
        return ShortVideoComment::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function repliesCountList($ids)
    {
        return ShortVideoComment::query()
            ->select('parent_id', DB::raw('count(*) as count'))
            ->whereIn('parent_id', $ids)
            ->groupBy('parent_id')
            ->pluck('count', 'parent_id')
            ->toArray();
    }

    public function deleteReplies($userId, $commentId)
    {
        $replies = ShortVideoComment::query()
            ->where('user_id', $userId)
            ->whereIn('parent_id', $commentId);
        $count = $replies->count();
        $replies->delete();
        return $count;
    }
}
