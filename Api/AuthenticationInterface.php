<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Api;

use Opengento\Document\Api\Data\DocumentInterface;
use Opengento\DocumentRestrict\Api\Data\AuthRequestInterface;

/**
 * @api
 */
interface AuthenticationInterface
{
    public function authenticate(DocumentInterface $document, AuthRequestInterface $authRequest): bool;
}
