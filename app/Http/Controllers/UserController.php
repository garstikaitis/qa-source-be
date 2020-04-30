<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUsers() {
        try {
			if(!Auth::user()->is_admin) abort(403, 'Access denied');
            return response(['success' => true, 'message' => 'Successfuly got users', 'data' => User::where('is_admin', 0)->get()], 200);
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
