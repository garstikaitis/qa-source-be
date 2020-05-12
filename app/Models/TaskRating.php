<?php

namespace App\Models;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TaskRating extends Model
{
	protected $fillable = ['rating', 'comment', 'created_by', 'given_to', 'taskId'];
	
	protected $table = 'task_user_ratings';

    public function createdBy() {
        return $this->hasOne(User::class, 'created_by', 'id');
    }

    public function givenTo() {
        return $this->hasOne(User::class, 'given_to', 'id');
    }

    public function task() {
        return $this->belongsTo(Task::class, 'taskId', 'id');
    }
}
