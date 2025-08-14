<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $val = Validator::make($request->all(),[
            "name"=>"required",
            "password"=>"required",
        ]);

        if($val->fails()){
            return response()->json([
                "message"=>"invalid fields"
            ],422);
        }

        $user = Auth::attempt([
            "name"=>$request->name,
            "password"=>$request->password,
        ]);
        
        if(!$user){
            return response()->json([
                "message"=>"invalid credentials"
            ],422);
        }

        $token = auth()->user()->createToken("koentji")->plainTextToken;

        return response()->json([
            "user"=>auth()->user()->name,
            "token"=>$token
        ]);
    }

    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json([
            "message"=>"user logged out succesfully"
        ]);
    }
}
