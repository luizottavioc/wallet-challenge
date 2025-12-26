<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\UseCase\LoginUseCase;
use App\Infrastructure\Enum\HttpCodesEnum;
use App\Infrastructure\Request\LoginRequest;
use App\Infrastructure\Trait\HttpResponseTrait;
use Psr\Http\Message\ResponseInterface;

class AuthController
{
    use HttpResponseTrait;

    public function __construct(
        protected LoginUseCase $loginUseCase
    ) {}

    public function login(LoginRequest $loginRequest): ResponseInterface
    {
        $loginInputDto = $loginRequest->getLoginInputDto();
        $loginOutputDto = $this->loginUseCase->login($loginInputDto);

        return $this->responseSuccess(
            HttpCodesEnum::OK,
            'Logged successfully',
            $loginOutputDto->toArray()
        );

    }
}
