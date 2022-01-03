<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginLoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'login'    => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string',
                'min:5',
            ],
            'captcha'  => ['required'],
        ];
    }
}
