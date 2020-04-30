<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'slug'];

    public function users() {
        return $this->belongsToMany(User::class, 'company_user', 'companyId', 'userId');
    }
}
