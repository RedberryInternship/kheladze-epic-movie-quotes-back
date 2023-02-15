<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\User;
use Illuminate\Http\Request;
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
}
