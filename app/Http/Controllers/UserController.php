<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function addEmail(Request $request)
    {
        $token = Str::random(64);
        $newEmail = Email::create([
            "email" => $request["email"],
            'primary' => false,
            'user_id' => $request['userId'],
            'token' => $token
        ]);

        Mail::send('email.email-verification', ['token' => $token, 'name' => $request['email']], function ($message) use ($request) {
            $message->to($request['email']);
            $message->subject('Email Verification Mail');
        });
        return $newEmail;
    }
    public function deleteEmail(Request $request)
    {
        $email = Email::where('id', $request[0])->first();
        $email->delete();
        return response()->json([
            'message' => 'You successfully deleted email'
        ]);
    }
    public function makePrimary(Request $request)
    {
        $primary = Email::where('id', $request[0])->first();
        $prevPrimary = Email::where('primary', 1)->first();

        $primary->primary = 1;
        $primary->save();

        $prevPrimary->primary = 0;
        $prevPrimary->save();
        return response()->json([
            'message' => 'You successfully updated email',
        ]);
    }
    public function changeUsername(Request $request)
    {
        $user = User::where('id', $request['userId'])->first();
        $user->name = $request['name'];
        $user->save();
        return response()->json([
            'message' => 'You successfully updated username',
        ]);
    }
    public function changePassword(Request $request)
    {
        $user = User::where('id', $request['userId'])->first();
        $user->password = bcrypt($request["password"]);
        $user->save();
        return response()->json([
            'message' => 'You successfully updated password',
        ]);
    }
    public function uploadImage(Request $request)
    {
        $path = request()->file('image')->store('users');
        $user = User::where('id', $request['userId'])->first();

        $user->image = $path;
        $user->save();

        return $user;
    }

    public function authGoogle(Request $request)
    {
        $user = User::where('id', $request['id'])->first();
        request()->session()->regenerate();
        Auth::login($user);
        return response(['user' => $user]);
    }

    public function sendInstructions(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $token = Str::random(64);

        $emailWithUser = Email::where('email', $request['email'])->with('users')->first();

        if ($emailWithUser->users->google_id) {
            throw ValidationException::withMessages(['email' => __('auth.google_user')]);
        }

        DB::table('password_resets')->insert([
            'email' => $emailWithUser->users->name,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('email.password-reset-email', ['token' => $token, 'name' => $emailWithUser->users->name,], function ($message) use ($request) {
            $message->to($request['email']);
            $message->subject('Reset Password');
        });

        return response()->json([
            'message' => $emailWithUser->users,
        ]);
    }
    public function redirectToReset($token)
    {
        $query = http_build_query(['reset' => $token]);
        $url = env('FRONTEND_URL') . '?' . $query;
        return redirect($url);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token'    => 'required',
            'password' => 'required|min:3|confirmed',
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'token' => $request['reset_token']
            ])
            ->first();

        $user = User::where('name', $updatePassword->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $updatePassword->email])->delete();

        return response()->json([
            'message' => $user,
        ]);
    }
}
