<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\LoginInputDto;
use App\Application\DTO\LoginOutputDto;

class LoginUseCase
{
    public function login(LoginInputDto $loginInputDto): LoginOutputDto {}
}