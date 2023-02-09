<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=>'required|max:255',
            'email'=>'required|unique:users|max:255',
            'password'=>'required|max:255',
        ]);

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);

        $token=$user->createToken('auth_token')->plainTextToken;

        return response([
            'message'=>'Registered Sucessfully',
            'token'=>$token,
            'token_type'=>'Bearer'
        ],201);
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required',
        ]);

        $user=User::where('email',$request->email)->first();

        if(!$user||!Hash::check($request->password,$user->password)){
            throw ValidationException::withMessages([
                'message'=>['The provided credentials are incorrect']
            ]);
        }

        $token=$user->createToken('auth_token')->plainTextToken;
        return response([
            'message'=>'Logged in sucesfully',
            'token'=>$token,
            'token_type'=>'Bearer'
        ],201);



    }

    public function logout(Request $request){

        $request->user()->tokens()->delete();

        return response([
            'message'=>'user logged out succesfully'
        ],201);

    }
}
