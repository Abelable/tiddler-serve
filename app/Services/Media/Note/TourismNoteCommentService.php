<?php

namespace App\Services\Media\Note;

use App\Models\TourismNoteComment;
use App\Services\BaseService;
use App\Utils\Inputs\CommentInput;
use App\Utils\Inputs\CommentListInput;
use Illuminate\Support\Facades\DB;

class TourismNoteCommentService extends BaseService
{
    public function pageList(CommentListInput $input, $columns = ['*'])
    {
        $query = TourismNoteComment::query();
        return $query
            ->where('parent_id', $input->commentId ?? 0)
            ->with('userInfo')
            ->where('note_id', $input->mediaId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function newComment($userId, CommentInput $input)
    {
        $comment = TourismNoteComment::new();
        $comment->user_id = $userId;
        $comment->note_id = $input->mediaId;
        $comment->content = $input->content;
        if (!empty($input->commentId)) {
            $comment->comment_id = $input->commentId;
        }
        $comment->save();
        return $comment;
    }

    public function getComment($userId, $id, $columns = ['*'])
    {
        return TourismNoteComment::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function repliesCountList($ids)
    {
        return TourismNoteComment::query()
            ->select('parent_id', DB::raw('count(*) as count'))
            ->whereIn('parent_id', $ids)
            ->groupBy('parent_id')
            ->pluck('count', 'parent_id')
            ->toArray();
    }

    public function deleteReplies($userId, $commentId)
    {
        $replies = TourismNoteComment::query()
            ->where('user_id', $userId)
            ->where('parent_id', $commentId);
        $count = $replies->count();
        $replies->delete();
        return $count;
    }

    public function deleteList($noteId)
    {
        return TourismNoteComment::query()->where('note_id', $noteId)->delete();
    }
}
