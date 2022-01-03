<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreatePromocodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code'     => [
                'required',
            ],
            'usages'   => [
                'required',
            ],
            'expires'  => [
                'required',
            ],
            'sum'      => [
                'required',
            ],
            'currency' => [
                'required',
            ],
        ];
    }
}
