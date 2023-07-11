<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetProjectListRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page' => 'required|integer',
            'perPage' => 'required|integer',
        ];
    }
}
