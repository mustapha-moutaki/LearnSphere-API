<?php

namespace App\Http\Controllers\api\V2;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'=>'required|email|max:255',
            'password' =>'required|string|min:8|max:255'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' =>'the provided credentials are incorrect'
            ], 401);
        }

       
        $token = $user->createToken($user->name, ['*'])->plainTextToken;

        return response()->json([
            'message'=>'login successful',
            'token_type' =>'Bearer',
            'token' =>$token,
        ], 200);
    }

    public function register(Request $request): JsonResponse{
        $request->validate([
           'name'=>'required|string|max:255',
           'email'=>'required|email|unique:users,email|max:255',
           'password' =>'required|string|min:8|max:255',
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password' => Hash::make($request->password),
        ]);

        if($user){
           
            $token = $user->createToken($user->name, ['*'])->plainTextToken;

            return response()->json([
                'message'=>'registration successful',
                'token_type' =>'Bearer',
                'token' =>$token,
            ], 201);
        } else {
            return response()->json([
                'message'=>'something went wrong',
            ], 500);
        }
    }


    public function profile(Request $request){
        if($request->user()){
            return response()->json([
                'message' =>'profile fiched',
                'data' =>$request->user()
            ], 200);
        }else{
            return response()->json([
                'message' =>'Not Authentificated',
            ], 401);
        }
    }
}
