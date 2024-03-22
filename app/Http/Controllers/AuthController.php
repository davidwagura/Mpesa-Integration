<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required|max:255',
            'email'=>'required|unique:users|max:255',
            'password'=>'required|max:255',
        ]);

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);

        $token=$user->createToken('auth_token')->plainTextToken;


        return response([
            'message' => 'registered succesfully',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required',
            'password'=>'required',
        ]);

        $user=User::where('email',$request->email)->first();

        if(!$user|| !Hash::check($request->password,$user->password))
        {
            throw ValidationException::withMessages([
                'message' => ['The provided credentials are incorrect']
            ]);
        }
        $token=$user->createToken('auth_token')->plainTextToken;

        return response([
            'message' => 'logged in succesfully',
            'token' => $token,
            'token_type' => 'Bearer'
        ],201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response([
            'masseage' => 'user logged out successfully'
        ],201);
    }
}
