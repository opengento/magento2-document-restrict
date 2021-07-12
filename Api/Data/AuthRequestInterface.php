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
    public function getPublicSecret(): ?string;

    public function getPrivateSecret(): ?string;
}
