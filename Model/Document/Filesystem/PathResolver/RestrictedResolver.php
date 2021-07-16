<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model\Document\Filesystem\PathResolver;

use Magento\Framework\Model\AbstractModel;
use Opengento\Document\Api\Data\DocumentTypeInterface;
use Opengento\Document\Model\Document\Filesystem\PathResolverInterface;

final class RestrictedResolver implements PathResolverInterface
{
    private const RESTRICT_PATH = 'downloadable';

    public function resolvePath(DocumentTypeInterface $documentType): string
    {
        return $this->isRestricted($documentType) ? self::RESTRICT_PATH : '';
    }

    private function isRestricted(DocumentTypeInterface $documentType): bool
    {
        return (bool) ($documentType instanceof AbstractModel
            ? $documentType->getData('is_restricted')
            : $documentType->getExtensionAttributes()->getIsRestricted());
    }
}
