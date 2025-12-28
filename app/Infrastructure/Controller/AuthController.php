<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Exception\GenericLoginException;
use App\Application\UseCase\LoginUseCase;
use App\Infrastructure\Enum\HttpCodesEnum;
use App\Infrastructure\Request\LoginRequest;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class AuthController extends AbstractController
{
    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        protected LoginUseCase $loginUseCase
    )
    {
        parent::__construct($request, $response);
    }

    /**
     * @throws GenericLoginException
     */
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
