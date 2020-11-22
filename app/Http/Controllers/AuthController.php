<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Notifications\ForgotPasswordNotif;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\File;


class AuthController extends Controller
{
    protected $time;

    public function __construct()
    {
        $time = 10;
        $this->time = $time;
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',

            ]
        ]);
        $user = User::where('email', $request->get('email'))->first();

        $token = $user->api_token;
        DB::table('password_resets')->insert([
            'token' => $token,
            'email' => $user->email,
            'created_at' => now()
        ]);


        if ($user) {

            $user->notify(new ForgotPasswordNotif($token));
        }
        $url = route('resetPassword') . '?api_token=' . $token;
        $responde = [
            'User' => $user,
            'Param' => 'email',
            'Url' => $url
        ];

        return response()->json($responde, 200);
        //return redirect()->back();

    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'min:5',
                'confirmed',


            ]
        ]);
        $reset = DB::table('password_resets')->where('token', $request->get('api_token'))->first();

        $password_resets = DB::table('password_resets')->where('token', $request->get('api_token'));
        $createdDateTime = $reset->created_at;

        if (!$reset) {
            return session()->flush()->back();
        }

        if (now()->diffInMinutes(Carbon::make($createdDateTime)) >= $this->time) {
            $password_resets->delete();
            session()->flush('status', 'Expire token');
            return redirect()->back();
        }


        $user = User::where('email', $reset->email)->first();

        if (!$user) {
            session()->flush('status', 'Expire token');
            return redirect()->back();

        }
        $password = $request->get('password');


        $user->password = Hash::make($password);
        $user->save();
        Auth::login($user);

        $respond = [
            'User:' => $user,
            'Param' => 'password,password_confirmation',
            'Session' => 'status'
        ];


        return response()->json($respond, 201);
    }

    public function register(Request $request)
    {
        if($request->file('images')){
            if($request->file('images')->isValid()){
                $images = $request->file('images');
                $new_name = rand().'.'.$images->extension();
                $img = $request->images->storeAs('', $new_name);
            }

        }
        $user = User::Create([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'api_token' => Str::random(64),
            'images'=>$img

        ]);

        Auth::login($user);
//        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        $user->roles()->attach($userRole);

        //$user->roles()->sync([1,2]);

        $user->signin = [
            'href' => '/api/signin',
            'method' => 'POST',
            'params' => 'email, password'
        ];
        $user->register = [
            'href' => '/api/register',
            'method' => 'POST',
            'params' => 'first_name, last_name, email, password, images'
        ];
        $response = [
            'msg' => 'User Created',
            'user' => $user,
            'userRole' => $userRole
        ];
        return response()->json($response, 201);
    }

    public function loginUser(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $token = Str::random(64);
            $request->user()->forceFill([
                'api_token' => $token,
            ])->save();

            return ['token' => $token];

        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }


    }


}
