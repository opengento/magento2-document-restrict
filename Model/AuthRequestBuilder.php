<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Phrase;
use Opengento\DocumentRestrict\Api\Data\AuthRequestInterface;
use Opengento\DocumentRestrict\Api\Data\AuthRequestInterfaceFactory;

final class AuthRequestBuilder
{
    public const HTTP_PARAM_AUTH = 'auth';
    public const HTTP_PARAM_PUBLIC_SECRET = 'public_secret';
    public const HTTP_PARAM_PRIVATE_SECRET = 'private_secret';

    /**
     * @var AuthRequestInterfaceFactory
     */
    private $authRequestFactory;

    public function __construct(
        AuthRequestInterfaceFactory $authRequestFactory
    ) {
        $this->authRequestFactory = $authRequestFactory;
    }

    /**
     * @throws InputException
     */
    public function createFromRequest(RequestInterface $request): AuthRequestInterface
    {
        $auth = $request->getParam(self::HTTP_PARAM_AUTH, []);
        $publicSecret = $auth[self::HTTP_PARAM_PUBLIC_SECRET] ?? null;
        $privateSecret = $auth[self::HTTP_PARAM_PRIVATE_SECRET] ?? null;
        if ($publicSecret && $privateSecret) {
            return new AuthRequest($publicSecret, $privateSecret);
        }

        throw new InputException(
            new Phrase(
                'Please check that %s and %s are sent.',
                [self::HTTP_PARAM_PUBLIC_SECRET, self::HTTP_PARAM_PRIVATE_SECRET]
            )
        );
    }
}
