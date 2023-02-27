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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;


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
                if (!$email->email_verified_at) {
                    throw ValidationException::withMessages(['email' => __('auth.email')]);
                }
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
        if (!$user->emails->where('email_verified_at', '<>', null)->first()) {
            throw ValidationException::withMessages(['email' => __('auth.email')]);
        }

        request()->session()->regenerate();

        Auth::login($user);
        return response(['user' => auth()->user()]);
    }
    public function me()
    {
        $user = Auth::user();
        $withEmail = User::where('id', $user->id)->with(['emails', 'notifications.writer'])->first();
        $withEmail->image = Storage::url($withEmail->image);
        $withEmail->notifications->map(function ($notification) {
            if (strpos($notification->writer->image, 'storage') == false) {
                $notification->writer->image = Storage::url($notification->writer->image);
            }
            return $notification;
        });
        return response(['user' => $withEmail, 'image' => Storage::url($withEmail->image)]);
    }

    public function register(StoreUserRequest $request)
    {
        $credentials = $request->validated();
        $token = Str::random(64);
        $user = User::create([
            "name" => $credentials["name"],
            'password' => bcrypt($credentials["password"]),
        ]);
        $user->image = 'users/person.png';
        $user->save();

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
        ]);
    }

    public function logout()
    {
        request()->session()->invalidate();
        return response()->json([
            'message' => 'You successfully logged out'
        ]);
    }

    public function verifyAccount($token)
    {
        $email = Email::where('token', $token)->first();

        if (!is_null($email)) {
            $email->email_verified_at = now();
            $email->save();
        }
        $query = http_build_query(['account_activated' => 1]);
        $url = env('FRONTEND_URL') . '?' . $query;
        return redirect($url);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $google_user = Socialite::with('google')->stateless()->user();

        $user = User::where('google_id', $google_user->getId())->first();
        if (!$user) {
            $emailAlreadyExists = Email::where('email', $google_user->getEmail())->first();
            if ($emailAlreadyExists) {
                $query = http_build_query(['email' => 'email_already_exists']);
                $url = env('FRONTEND_URL') . '?' . $query;
                return redirect($url);
            }
            $new_user = User::create([
                "name" => $google_user->getName(),
                'google_id' => $google_user->getId(),
            ]);
            $new_user->image = 'users/person.png';
            $new_user->save();
            $email = Email::create([
                "email" => $google_user->getEmail(),
                'user_id' => $new_user->id,
            ]);
            $email->email_verified_at = now();
            $email->save();

            $query = http_build_query(['googleuser' => $new_user->id]);
            $url = env('FRONTEND_URL') . '?' . $query;

            Auth::login($new_user);
            return redirect($url);
        } else {
            $query = http_build_query(['googleuser' => $user->id]);
            $url = env('FRONTEND_URL') . '?' . $query;

            Auth::login($user);
            return redirect($url);
        }
        try {
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
