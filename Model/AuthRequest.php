<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model;

use Opengento\DocumentRestrict\Api\Data\AuthRequestInterface;

final class AuthRequest implements AuthRequestInterface
{
    /**
     * @var string|null
     */
    private $publicSecret;

    /**
     * @var string|null
     */
    private $privateSecret;

    /**
     * @var int|null
     */
    private $customerId;

    public function __construct(
        ?string $publicSecret,
        ?string $privateSecret,
        ?int $customerId
    ) {
        $this->publicSecret = $publicSecret;
        $this->privateSecret = $privateSecret;
        $this->customerId = $customerId;
    }

    public function hasAuth(): bool
    {
        return $this->customerId || $this->privateSecret;
    }

    public function isCustomerAuth(): bool
    {
        return (bool) $this->customerId;
    }

    public function getPublicSecret(): ?string
    {
        return $this->publicSecret;
    }

    public function getPrivateSecret(): ?string
    {
        return $this->privateSecret;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }
}
