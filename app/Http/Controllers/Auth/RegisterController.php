<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Helpers\FormHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'nullable|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ], FormHelpers::validationMessages());

        
        if ($validator->fails()) {
            abort(403, $validator->errors()->first());
        }

        return $validator;
    }

    public function register(Request $request)
    {

        
        $this->validator($request->all());

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return new Response(['success' => true, 'data' => $user], 201);

    }

    protected function registered(Request $request, $user)
    {
        $user->generateToken();

        return response()->json(['success' => true, 'data' => $user->toArray()], 201);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => array_key_exists('name', $data) ? $data['name'] : 'Unnamed',
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
