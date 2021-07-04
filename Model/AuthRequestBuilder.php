<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Opengento\DocumentRestrict\Api\Data\AuthRequestInterface;
use Opengento\DocumentRestrict\Api\Data\AuthRequestInterfaceFactory;

final class AuthRequestBuilder
{
    public const HTTP_PARAM_PUBLIC_SECRET = 'public_secret';
    public const HTTP_PARAM_PRIVATE_SECRET = 'private_secret';

    /**
     * @var AuthRequestInterfaceFactory
     */
    private $authRequestFactory;

    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(
        AuthRequestInterfaceFactory $authRequestFactory,
        Session $customerSession
    ) {
        $this->authRequestFactory = $authRequestFactory;
        $this->customerSession = $customerSession;
    }

    public function createFromRequest(RequestInterface $request): AuthRequestInterface
    {
        return $this->authRequestFactory->create(
            $request->getParam(self::HTTP_PARAM_PUBLIC_SECRET),
            $request->getParam(self::HTTP_PARAM_PRIVATE_SECRET),
            $this->customerSession->getCustomerId() ? (int)$this->customerSession->getCustomerId()() : null
        );
    }
}
