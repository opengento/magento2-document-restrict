<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model\ResourceModel\DocumentType\Relation;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;
use Opengento\Document\Api\Data\DocumentTypeInterface;
use Opengento\DocumentRestrict\Model\ResourceModel\Migrate;

final class Restrict implements RelationInterface
{
    /**
     * @var Migrate
     */
    private $migrate;

    public function __construct(
        Migrate $migrate
    ) {
        $this->migrate = $migrate;
    }

    public function processRelation(AbstractModel $object): void
    {
        if (
            $object instanceof DocumentTypeInterface &&
            $object->getOrigData('is_restricted') !== $object->getData('is_restricted')
        ) {
            $this->migrate->scheduleMigration((int) $object->getId());
        }
    }
}
