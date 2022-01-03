<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateVipRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start'      => [
                'required',
            ],
            'level_name' => [
                'required',
            ],
            'level'      => [
                'required',
            ],
        ];
    }
}
