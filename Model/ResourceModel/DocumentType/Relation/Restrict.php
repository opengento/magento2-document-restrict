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
use Opengento\DocumentRestrict\Model\DocumentType\Visibility;

final class Restrict implements RelationInterface
{
    public function processRelation(AbstractModel $object): void
    {
        if ($object instanceof DocumentTypeInterface) {
            $origVisibility = $object->getOrigData('visibility');
            $visibility = $object->getVisibility();

            if ($origVisibility !== $visibility) {
                if ($origVisibility === Visibility::VISIBILITY_RESTRICT) {
                    //todo Move private to public: use batch action; DocumentType visibility shouldn't be updated till all is moved
                } elseif ($visibility === Visibility::VISIBILITY_RESTRICT) {
                    //todo Move public to private: use batch action; DocumentType visibility shouldn't be updated till all is moved
                }
            }
        }
    }
}
