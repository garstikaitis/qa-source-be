<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUsers() {
        try {
            $data = null;
            if(Auth::user()->isTester()) abort(403, 'Access denied');
            if(Auth::user()->isClient()) {
                $company = Auth::user()->companies()->first()->load('users');
                $data = $company->users;
            } else {
                $data = User::with('companies')->where('is_admin', 0)->get();
            }
            return response(['success' => true, 'message' => 'Successfuly got users', 'data' => $data], 200);
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getUserData() {
        try {
            $data = null;
            $data['ratings'] = Auth::user()->ratings();   
            return response(['success' => true, 'message' => 'Successfuly got users', 'data' => $data], 200);
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
