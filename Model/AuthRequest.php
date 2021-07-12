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

    public function __construct(
        ?string $publicSecret,
        ?string $privateSecret
    ) {
        $this->publicSecret = $publicSecret;
        $this->privateSecret = $privateSecret;
    }

    public function getPublicSecret(): ?string
    {
        return $this->publicSecret;
    }

    public function getPrivateSecret(): ?string
    {
        return $this->privateSecret;
    }
}
