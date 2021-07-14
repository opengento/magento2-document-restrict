<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Api;

use Opengento\DocumentRestrict\Api\Data\AuthRequestInterface;
use Opengento\DocumentRestrict\Api\Data\RestrictInterface;

/**
 * @api
 */
interface AuthenticationInterface
{
    public function authenticate(int $typeId, AuthRequestInterface $authRequest): bool;

    public function setPrivateSecret(RestrictInterface $restrict, string $privateSecret): RestrictInterface;
}
