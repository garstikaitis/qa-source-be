<?php

namespace App\Http\Controllers;

use App\Model\Task;
use App\Http\UseCases\Task\CreateTask;

class TaskController extends Controller
{
    public function createTask() {
        try {
            return (new CreateTask(request()->all()))->handle();
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTasks() {
        try {
            return response(['success' => true, 'message' => 'Successfuly got data', 'data' => Task::all()], 200);
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
