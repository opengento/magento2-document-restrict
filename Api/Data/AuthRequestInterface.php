<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Api\Data;

/**
 * @api
 */
interface AuthRequestInterface
{
    public function hasAuth(): bool;

    public function isCustomerAuth(): bool;

    public function getPublicSecret(): ?string;

    public function getPrivateSecret(): ?string;

    public function getCustomerId(): ?int;
}
