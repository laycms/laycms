<?php

declare(strict_types=1);

namespace App\Modules\Admin\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RolePermissionCreateRequest',
    required: [
        'tenant_id',
    ],
    properties: [
        new OA\Property(property: 'tenant_id', description: '租户ID', type: 'integer'),
    ]
)]
class RolePermissionCreateRequest extends FormRequest
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
            'tenant_id' => 'require',
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
            'tenant_id.require' => '请设置租户ID',
        ];
    }
}
