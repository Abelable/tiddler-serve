<?php

namespace App\Services\Media\ShortVideo;

use App\Models\ShortVideoComment;
use App\Services\BaseService;
use App\Utils\Inputs\CommentInput;
use App\Utils\Inputs\CommentListInput;

class ShortVideoCommentService extends BaseService
{
    public function pageList(CommentListInput $input, $columns = ['*'])
    {
        $query = ShortVideoComment::query();
        if (!empty($input->commentId)) {
            $query->where('comment_id', $input->commentId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function newComment($userId, CommentInput $input)
    {
        $comment = ShortVideoComment::new();
        $comment->user_id = $userId;
        $comment->video_id = $input->mediaId;
        $comment->content = $input->content;
        if (!empty($input->commentId)) {
            $comment->comment_id = $input->commentId;
        }
        $comment->save();
        return $comment;
    }

    public function getComment($userId, $id, $columns = ['*'])
    {
        return ShortVideoComment::query()->where('user_id', $userId)->find($id, $columns);
    }
}
