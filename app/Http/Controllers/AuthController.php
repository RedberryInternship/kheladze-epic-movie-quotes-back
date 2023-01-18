<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Email;
use App\Models\User;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    use HasApiTokens;

    public function login(StoreLoginRequest $request)
    {
        $name_email = Str::contains($request['email'], '@') ? 'email' : 'name';
        $error = ValidationException::withMessages(['password' => __('auth.failed')]);
        $attributes = $request->validated();
        if ($name_email == 'email') {
            $email = Email::where('email', $attributes['email'])->with(['users'])->first();
            if (!$email) {
                throw $error;
            } elseif ($email) {
                if (!Hash::check($attributes['password'], $email->users->password)) {
                    throw $error;
                }
            }
        } elseif ($name_email == 'name') {
            if (!auth()->attempt([
                'name' => $attributes['email'], 'password' => $attributes['password']
            ])) {
                throw $error;
            }
        }

        $user = $name_email == 'email' ? User::where('id', $email->user_id)->with(['emails'])->first() : User::where('name', $request['email'])->with(['emails'])->first();

        session()->regenerate();

        return response()->json([
            'message' => "Success",
            'user' => $user,
            'token' => $user->createToken('API Token for ' . $user['name'])->plainTextToken
        ]);
    }

    public function register(StoreUserRequest $request)
    {
        $credentials = $request->validated();
        $token = Str::random(64);
        $user = User::create([
            "name" => $credentials["name"],
            'password' => bcrypt($credentials["password"])
        ]);

        $email = Email::create([
            "email" => $credentials["email"],
            'primary' => true,
            'user_id' => $user->id,
            'token' => $token
        ]);

        Mail::send('email.email-verification', ['token' => $token, 'name' => $user->name], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Email Verification Mail');
        });

        return response()->json([
            'message' => "Success",
            'code' => 201,
            'user' => $user,
            'token' => $user->createToken('API Token for ' . $user['name'])->plainTextToken,
        ]);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json([
            'message' => 'You successfully logged out, your token has been deleted'
        ]);
    }

    public function verifyAccount($token)
    {
        $email = Email::where('token', $token)->first();

        if (!is_null($email)) {
            $email->email_verified_at = now();
            $email->save();
        }

        return redirect(env('MAIL_TO_URL'));
    }
}
