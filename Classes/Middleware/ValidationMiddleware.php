<?php
declare(strict_types=1);

/*
 * This file is part of the "messenger" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Messenger\Middleware;


use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;
use WapplerSystems\Messenger\Exception\ValidationFailedException;

final class ValidationMiddleware implements MiddlewareInterface
{
    private ValidatorResolver $validatorResolver;

    public function __construct(ValidatorResolver $validatorResolver)
    {
        $this->validatorResolver = $validatorResolver;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        $validator = $this->validatorResolver->getBaseValidatorConjunction(\get_class($message));

        $errorResult = $validator->validate($message);

        if (\count($errorResult->getFlattenedErrors()) > 0) {
            throw new ValidationFailedException($message, $errorResult);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
