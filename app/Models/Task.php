<?php

namespace App\Model;

use App\Models\Company;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['name', 'description', 'companyId'];

    public function company() {
        return $this->belongsTo(Company::class, 'companyId', 'id');
    }

    public function project() {
        return $this->hasOne(Project::class, 'projectId', 'id');
    }
}
