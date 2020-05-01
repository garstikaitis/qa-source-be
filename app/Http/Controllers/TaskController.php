<?php

namespace App\Http\Controllers;

use App\Model\Task;
use Illuminate\Support\Facades\Auth;
use App\Http\UseCases\Task\CreateTask;

class TaskController extends Controller
{
    public function createTask() {
        try {
            return (new CreateTask(request()->all()))->handle();
        } catch (\Throwable $e) {
            echo $e->getMessage();
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTasks() {
        try {
            $data = null;
            if(Auth::user()->isTester()) {
                $data = Task::whereDoesntHave('projects')
            }
            if(Auth::user()->isClient()) {
                $data = Task::where('companyId', Auth::user()->companies()->first()->id)->with('company')->get();
            } else {
                $data = Task::with('company')->get();
            }
            return response(['success' => true, 'message' => 'Successfuly got data', 'data' => $data], 200);
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
