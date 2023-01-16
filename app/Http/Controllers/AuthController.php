<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return "this is login method";
    }
    public function register(StoreUserRequest $request)
    {
        $credentials = $request->validated();
        dd($credentials);
        return "this is register method";
    }
    public function logout()
    {
        return "this is logout method";
    }
}
