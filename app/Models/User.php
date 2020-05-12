<?php

namespace App\Models;

use App\Models\Company;
use App\Models\TaskRating;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const ADMIN = 'admin';
    const CLIENT = 'client';
    const TESTER = 'tester';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['role'];

    public function getRoleAttribute() {
        return $this->determineRole();
    }

    public function ratings() {
        return TaskRating::where('given_to', $this->id)->get();
    }

    private function determineRole() {
        if($this->isTester()) { return self::TESTER; }
        if($this->isClient()) { return self::CLIENT; }
        if($this->isAdmin()) { return self::ADMIN; }
    }

    public function generateToken()
    {
        $this->api_token = Str::random(60);
        $this->save();

        return $this->api_token;
    }

    public function companies() {
        return $this->belongsToMany(Company::class, 'company_user', 'userId', 'companyId');
    }

    public function isTester() {
        return $this->companies()->count() === 0 && !$this->is_admin;
    }

    public function isClient() {
        return $this->companies()->count() > 0 && !$this->is_admin;
    }

    public function isAdmin() {
        return $this->is_admin;
    }
}
