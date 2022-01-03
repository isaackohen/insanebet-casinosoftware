<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => [
                'required',
                'unique:users',
                'email',
            ],
            'name'     => [
                'required',
                'unique:users',
                'string',
                'max:24',
                'regex:/^[a-zA-Z0-9]{5,24}$/u',
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
