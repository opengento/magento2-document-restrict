<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model\Restrict;

use Opengento\DocumentRestrict\Api\Data\RestrictInterface;

final class Registry implements RegistryInterface
{
    /**
     * @var RestrictInterface|null
     */
    private $restrict;

    public function get(): RestrictInterface
    {
        return $this->restrict;
    }

    public function set(RestrictInterface $restrict): void
    {
        $this->restrict = $restrict;
    }
}
