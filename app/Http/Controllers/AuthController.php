<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\WelcomeMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create($fields);
        $token = $user->createToken($request->name);

        event(new Registered($user));

        Mail::to('myassistantprogram@gmail.com')->send(new WelcomeMail(Auth::user()));

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        // get email
        $user = User::where('email', $request->email)->first();
        // check email and password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return ['message' => 'The provided credentials are incorrect.'];
        }

        $token = $user->createToken($user->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ['message' => 'You are logged out.'];
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return [
            "data" => 'fff'
        ];
    }
}
