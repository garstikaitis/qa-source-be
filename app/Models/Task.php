<?php

namespace App\Models;

use App\Models\File;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['name', 'description', 'companyId', 'file_id', 'type'];

    public function company() {
        return $this->belongsTo(Company::class, 'companyId', 'id');
    }

    public function project() {
        return $this->hasOne(Project::class, 'taskId');
    }

    public function file() {
        return $this->belongsTo(File::class);
    }
    
    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }
}
