<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AvatarUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['image' => [
            'required',
            'image',
            'mimes:jpeg,png,jpg,svg',
            'max:2048',
        ]];
    }
}
