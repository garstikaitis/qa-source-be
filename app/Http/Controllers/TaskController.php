<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\UseCases\Task\RateTask;
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

    public function rateTask() {
        try {
            return (new RateTask(request()->all()))->handle();
        } catch (\Throwable $e) {
            echo $e->getMessage();
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTasks() {
        try {
            $data = null;
            if(Auth::user()->isTester()) {
                $data = Task::with(['project', 'company'])->whereDoesntHave('project')->get();
                return response(['success' => true, 'message' => 'Successfuly got data', 'data' => $data], 200);
            }
            if(Auth::user()->isClient()) {
                $data = Task::where('companyId', Auth::user()->companies()->first()->id)->with('company')->get();
                return response(['success' => true, 'message' => 'Successfuly got data', 'data' => $data], 200);
            } else {
                $data = Task::with('company')->get();
                return response(['success' => true, 'message' => 'Successfuly got data', 'data' => $data], 200);
            }
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTask() {
        try {
            $task = Task::with('company', 'file')->where('id', request()->get('taskId'))->firstOrFail();
            return response(['success' => true, 'message' => 'Succesfuly loaded task', 'data' => $task]);
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
