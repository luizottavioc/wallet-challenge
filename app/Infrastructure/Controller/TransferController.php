<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Exception\UnauthorizedTransferException;
use App\Application\UseCase\TransferUseCase;
use App\Domain\Exception\CannotSubtractAmountException;
use App\Domain\Exception\InvalidValueObjectArgumentException;
use App\Domain\Exception\UserHasNoFundsToPerformTransferException;
use App\Domain\Exception\UserTypeCannotPerformTransferException;
use App\Infrastructure\Enum\HttpCodesEnum;
use App\Infrastructure\Request\TransferRequest;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class TransferController extends AbstractController
{
    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        protected TransferUseCase $transferUseCase
    )
    {
        parent::__construct($request, $response);
    }

    /**
     * @throws UnauthorizedTransferException
     * @throws CannotSubtractAmountException
     * @throws InvalidValueObjectArgumentException
     * @throws UserHasNoFundsToPerformTransferException
     * @throws UserTypeCannotPerformTransferException
     */
    public function transfer(TransferRequest $transferRequest): ResponseInterface
    {
        $transferInputDto = $transferRequest->getTransferInputDto();
        $transferOutputDto = $this->transferUseCase->transfer($transferInputDto);

        return $this->responseSuccess(
            HttpCodesEnum::OK,
            'Transfer perform successfully',
            $transferOutputDto->toArray()
        );
    }
}
