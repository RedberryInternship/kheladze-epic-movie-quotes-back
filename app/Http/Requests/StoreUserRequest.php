<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'     => 'required|min:3|unique:users,name',
            'email'    => 'required|unique:emails,email',
            'password' => 'required|confirmed|min:3',
        ];
    }
}
