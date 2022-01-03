<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateChallengeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'game'       => [
                'required',
            ],
            'maxwinners' => [
                'required',
            ],
            'expires'    => [
                'required',
            ],
            'minbet'     => [
                'required',
            ],
            'multiplier' => [
                'required',
            ],
            'sum'        => [
                'required',
            ],
            'currency'   => [
                'required',
            ],
        ];
    }
}
