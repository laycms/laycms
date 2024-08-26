<?php

declare(strict_types=1);

namespace App\Modules\Admin\Requests\Subsidiary;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SubsidiaryUpdateRequest',
    required: [
        'id',

    ],
    properties: [
        new OA\Property(property: 'id', description: 'ID', type: 'integer'),

    ]
)]
class SubsidiaryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'require',

        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.require' => '请设置ID',

        ];
    }
}
