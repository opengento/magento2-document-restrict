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

final class Restrict implements RelationInterface
{
    public function processRelation(AbstractModel $object): void
    {
        if ($object instanceof DocumentTypeInterface) {
            $origIsRestricted = $object->getOrigData('is_restricted');
            $isRestricted = $object->getExtensionAttributes()->getIsRestricted();

            if ($origIsRestricted !== $isRestricted) {
                if ($origIsRestricted) {
                    //todo Move private to public: use batch action; DocumentType visibility shouldn't be updated till all is moved
                } elseif ($isRestricted) {
                    //todo Move public to private: use batch action; DocumentType visibility shouldn't be updated till all is moved
                }
            }
        }
    }
}
