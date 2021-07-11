<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface RestrictSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @inheritdoc
     * @return \Opengento\DocumentRestrict\Api\Data\RestrictInterface[]
     */
    public function getItems(): array;

    /**
     * @inheritdoc
     * @param \Opengento\DocumentRestrict\Api\Data\RestrictInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}
