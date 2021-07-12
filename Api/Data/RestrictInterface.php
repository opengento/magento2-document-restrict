<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Api\Data;

use DateTime;

/**
 * @api
 */
interface RestrictInterface
{
    public function getId(): ?int;

    public function getTypeId(): int;

    public function getPublicSecret(): ?string;

    public function getPrivateSecret(): ?string;

    public function getCreatedAt(): DateTime;

    public function getUpdatedAt(): DateTime;

    /**
     * @return \Opengento\DocumentRestrict\Api\Data\RestrictExtensionInterface
     */
    public function getExtensionAttributes(): RestrictExtensionInterface;

    /**
     * @param \Opengento\DocumentRestrict\Api\Data\RestrictExtensionInterface $extensionAttributes
     * @return \Opengento\DocumentRestrict\Api\Data\RestrictInterface
     */
    public function setExtensionAttributes(RestrictExtensionInterface $extensionAttributes): RestrictInterface;
}
