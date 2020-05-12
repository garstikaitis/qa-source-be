<?php

namespace App\Models;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    const REQUESTED = 'requested';
    const STARTED = 'started';
    const FINISHED = 'finished';

    protected $fillable = ['taskId', 'userId', 'status', 'submission_file_id'];

    public function tester() {
        return $this->hasOne(User::class, 'id', 'userId');
    }

    public function task() {
        // return $this->hasOne(Task::class, 'id', 'taskId');
        return $this->hasOne(Task::class, 'id', 'taskId');
    }
}
