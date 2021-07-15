<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Plugin\Model;

use Magento\Framework\Model\AbstractModel;
use Opengento\Document\Api\Data\DocumentTypeInterface;
use Opengento\Document\Api\DocumentTypeRepositoryInterface;

final class DocumentTypeRepository
{
    public function afterGetById(DocumentTypeRepositoryInterface $subject, DocumentTypeInterface $documentType): DocumentTypeInterface
    {
        if ($documentType instanceof AbstractModel) {
            $documentType->getExtensionAttributes()->setIsRestricted((bool) $documentType->getData('is_restricted'));
        }

        return $documentType;
    }
}
