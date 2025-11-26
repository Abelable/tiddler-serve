<?php

namespace App\Services;

use App\Models\SystemTodo;

class SystemTodoService extends BaseService
{
    public function getTodoList($columns=['*'])
    {
        return SystemTodo::query()
            ->where('status', 0)
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }

    public function createTodo($type, array $referenceIds)
    {
        foreach ($referenceIds as $referenceId) {
            $todo = SystemTodo::new();
            $todo->type = $type;
            $todo->reference_id = $referenceId;
            $todo->save();
        }
    }

    public function finishTodo($type, $referenceId)
    {
        $todo = SystemTodo::query()
            ->where('type', $type)
            ->where('reference_id', $referenceId)
            ->first();

        $todo->status = 1;
        $todo->save();

        return $todo;
    }

    public function deleteTodo($type, $referenceId)
    {
        SystemTodo::query()->where('type', $type)->where('reference_id', $referenceId)->delete();
    }
}
