<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePasswordResetRequest extends FormRequest
{

    public function rules()
    {
        return [
            'reset_token'    => 'required',
            'password' => 'required|min:3|confirmed',
        ];
    }
}
