<?php

namespace App\Http\Controllers\api\V2;

// use Storage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse{
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect'
            ], 401);
        }

        $token = $user->createToken($user->name, ['*'])->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token_type' => 'Bearer',
            'token' => $token,
        ], 200);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            // Delete existing tokens to prevent multiple active sessions
            $user->tokens()->delete();

            // Generate access token (valid for 15 minutes)
            $accessToken = $user->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;

            // Generate refresh token (valid for 7 days)
            $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(7))->plainTextToken;

            return response()->json([
                'message' => 'Registration successful',
                'token_type' => 'Bearer',
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ], 201);
        } else {
            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function profile(Request $request): JsonResponse
    {
        if ($request->user()) {
            return response()->json([
                'message' => 'Profile fetched',
                'data' => $request->user()
            ], 200);
        } else {
            return response()->json([
                'message' => 'Not Authenticated',
            ], 401);
        }
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required'
        ]);

        $user = User::whereHas('tokens', function ($query) use ($request) {
            $query->where('token', hash('sha256', $request->refresh_token))
                ->whereJsonContains('abilities', 'refresh');
        })->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }
        // Delete all old tokens
        $user->tokens()->delete();

        // Create a new access token (valid for 15 minutes)
        $accessToken = $user->createToken('access_token', ['*'], now()->addMinutes(1))->plainTextToken;

        // Create a new refresh token (valid for 7 days)
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(7))->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer'
        ], 200);
    }



    //update image to add image
   
    public function updateProfile(Request $request) {
        $user = auth()->user();
        
   
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);
        
       
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
       
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
    
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
     
        if ($request->hasFile('image')) {
           
            if ($user->image) {
                Storage::delete('public/' . $user->image);
            }
            
          
            $imagePath = $request->file('image')->store('images', 'public');
          
            $user->image = $imagePath;
        }
        
        
        $user->save();
        
        
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
            'image_url' => $user->image ? Storage::url($user->image) : null,
        ], 200);
    }

//shoe all roles 

  

}
