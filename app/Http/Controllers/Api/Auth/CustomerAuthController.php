<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;



class CustomerAuthController extends Controller
{

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        //check email
        $user = Customer::where('email', $fields['email'])->first();

        //check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds',
            ], 401);
        }
        if ($user->status == 'lock' || $user->status == 'banned') {
            return response([
                'message' => 'Tài khoản của bạn đã bị khoá hoặc bị cấm',
            ], 400);
        }
        $token = $user->createToken('mytoken_player')->plainTextToken;
        $results = [
            'email_verified_at' => $user->email_verified_at,
            'token' => $token
        ];
        return response($results, 200);
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'f_name' => 'required|string',
            'email' => 'required|unique:customers',
            'phone' => 'required|unique:customers',
            'password' => 'required|string',
        ]);
        $player = Customer::create([
            'f_name' => $request->f_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);
        $token = $player->createToken('mytoken_player')->plainTextToken;
        return response([
            //'results' => $player,
            'token' => $token,
            'is_phone_verified' => 0,
        ], 200);
    }

    public function forgot_password(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $user = Customer::where('username', $fields['username'])->first();
        $user->password =  bcrypt($fields['password']);
        $user->save();
        return response([
            'results' => $user,
        ], 200);
    }



    // public function logout(Request $request)
    // {
    //     // dd(auth()->user()->tokens());
    //     auth()->user()->tokens()->delete();
    //     Session:flush();
    //     return [
    //         'message' => "Logged out"
    //     ];
    // }
}
