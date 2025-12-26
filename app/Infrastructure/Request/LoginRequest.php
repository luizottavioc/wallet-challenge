<?php

declare(strict_types=1);

namespace App\Infrastructure\Request;

use App\Application\DTO\LoginInputDto;
use Hyperf\Validation\Request\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function getEmail(): string
    {
        return $this->input('email');
    }

    public function getPassword(): string
    {
        return $this->input('password');
    }

    public function getLoginInputDto(): LoginInputDto
    {
        return new LoginInputDto(
            email: $this->getEmail(),
            password: $this->getPassword(),
        );
    }
}
