<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use App\Mail\WelcomeMail;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

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
            // Mail::to('myassistantprogram@gmail.com')->send(new WelcomeMail(Auth::user()));
    
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

    public function forgotPass(Request $request) : JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            $fields = $request->validate([
                'email' => 'required|email|unique:users,email,'.$user->id,
                'verify_code' => 'required|max:5'
            ]);
        
            $user->update($fields);
            
            // send mail with verify code
            // Mail::to('myassistantprogram@gmail.com')->send(new ForgotPassword($user));
    
            return response()->json([
                'status' => true,
                'data' => $user,
                'message' => 'Getting verification code successful'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function resetPass(Request $request) : JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            $fields = $request->validate([
                'email' => 'required|email|unique:users,email,'.$user->id,
                'password' => 'required|confirmed'
            ]);
        
            $user->update($fields);
    
            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Update Password successful'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
