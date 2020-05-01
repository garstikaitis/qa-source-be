<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\FormHelpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function validateLogin(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ], FormHelpers::validationMessages());

        
        if ($validator->fails()) {
            abort(403, $validator->errors()->first());
        }

        return $validator;
    }

    // public function attemptLogin(Request $request) {
    //     return Auth::attempt($request->only(['email', 'password']));
    // }

    public function login(Request $request) {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $user->generateToken();

            return response()->json([
                'success' => true,
                'data' => $user,
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json(['success' => true, 'data' => 'User logged out.'], 200);
    }
}
