<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use App\Application\Contract\AuthenticatorInterface;
use App\Infrastructure\Exception\InvalidTokenException;
use Hyperf\Context\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected ContainerInterface $container,
        protected ResponseInterface $response,
        protected RequestInterface $request,
        protected AuthenticatorInterface $authenticator
    ) {}

    /**
     * @throws InvalidTokenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authorization = $this->getAuthorizationHeader();
        $token = $this->getTokenByAuthorizationHeader($authorization);

        $this->checkTokenIsValid($token);
        $this->setTokenInfoOnContext($token);

        return $handler->handle($request);
    }

    /**
     * @throws InvalidTokenException
     */
    private function getAuthorizationHeader(): string
    {
        $authorization = $this->request->getHeader('Authorization');
        if (empty($authorization)) {
            throw new InvalidTokenException();
        }

        return $authorization[0];
    }

    /**
     * @throws InvalidTokenException
     */
    private function getTokenByAuthorizationHeader(string $authorization): string
    {
        $authorizationParts = explode(' ', $authorization[0], 2);
        if (count($authorizationParts) !== 2 || strtolower($authorizationParts[0]) !== 'bearer') {
            throw new InvalidTokenException();
        }

        return $authorizationParts[1];
    }

    /**
     * @throws InvalidTokenException
     */
    private function checkTokenIsValid(string $token): void
    {
        $tokenIsValid = $this->authenticator->tokenIsValid($token);
        if ($tokenIsValid) {
            return;
        }

        throw new InvalidTokenException();
    }

    private function setTokenInfoOnContext(string $token): void
    {
        $authEntity = $this->authenticator->decodeToken($token);

        Context::set('auth.user_id', $authEntity->getUserId());
        Context::set('auth.user_type', $authEntity->getUserType());
        Context::set('auth.token', $authEntity->getToken());
    }
}
