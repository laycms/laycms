<?php

declare(strict_types=1);

namespace App\Modules\Admin\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserCreateRequest',
    required: [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
    ],
    properties: [
        new OA\Property(property: 'name', description: '', type: 'string'),
        new OA\Property(property: 'email', description: '', type: 'string'),
        new OA\Property(property: 'email_verified_at', description: '', type: 'string'),
        new OA\Property(property: 'password', description: '', type: 'string'),
        new OA\Property(property: 'remember_token', description: '', type: 'string'),
    ]
)]
class UserCreateRequest extends FormRequest
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
            'name' => 'require',
            'email' => 'require',
            'email_verified_at' => 'require',
            'password' => 'require',
            'remember_token' => 'require',
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
            'name.require' => '请设置',
            'email.require' => '请设置',
            'email_verified_at.require' => '请设置',
            'password.require' => '请设置',
            'remember_token.require' => '请设置',
        ];
    }
}
