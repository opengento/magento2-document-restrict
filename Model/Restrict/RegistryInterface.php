<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model\Restrict;

use Opengento\DocumentRestrict\Api\Data\RestrictInterface;

/**
 * @api
 */
interface RegistryInterface
{
    public function get(): RestrictInterface;

    public function set(RestrictInterface $restrict): void;
}
