<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Mail\WelcomeMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request) : JsonResponse
    {
        try {
            $fields = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed',
                'verify_code' => 'required|max:5'
            ]);
    
            $user = User::create($fields);
            $token = $user->createToken($request->name);
    
            event(new Registered($user)); 

            // send mail with verify code
            Mail::to('myassistantprogram@gmail.com')->send(new WelcomeMail(Auth::user()));
    
            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token->plainTextToken,
                'message' => 'Register successful'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function login(Request $request) : JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users',
                'password' => 'required'
            ]);
    
            // get email
            $user = User::where('email', $request->email)->first();
            // check email and password
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'The provided credentials are incorrect.'
                ], 401);
            }
    
            $token = $user->createToken($user->name);
    
            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token->plainTextToken,
                'message' => 'Login successful'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request) : JsonResponse
    {
        try {
            $user = User::where('id', $request->user()->id)->first();
            if ($user) {
                $user->tokens()->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'You are logged out.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {    
            $fields = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed',
                'verify_code' => 'required|max:5'
            ]);
    
            $user->update($fields);

            return response()->json([
                'status' => true,
                'data' => $user,
                'message' => "Successful update user's info"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
