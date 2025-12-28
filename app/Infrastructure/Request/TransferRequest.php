<?php

declare(strict_types=1);

namespace App\Infrastructure\Request;

use App\Application\DTO\TransferInputDto;
use App\Infrastructure\Trait\RequestContextTrait;
use Hyperf\Validation\Request\FormRequest;

class TransferRequest extends FormRequest
{
    use RequestContextTrait;

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
            'payeeId' => 'required|uuid|exists:users,id',
            'amount' => 'required|int|min:1'
        ];
    }

    public function getPayeeId(): string
    {
        return $this->input('payeeId');
    }

    public function getAmount(): int
    {
        return $this->input('amount');
    }

    public function getTransferInputDto(): TransferInputDto
    {
        return new TransferInputDto(
            payerId: $this->getContextUserId(),
            payeeId: $this->getPayeeId(),
            amount: $this->getAmount()
        );
    }
}
