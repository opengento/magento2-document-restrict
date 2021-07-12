<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model\Document\Filesystem\PathResolver;

use Opengento\Document\Api\Data\DocumentTypeInterface;
use Opengento\Document\Model\Document\Filesystem\PathResolverInterface;
use Opengento\DocumentRestrict\Model\DocumentType\Visibility;

final class VisibilityResolver implements PathResolverInterface
{
    private const RESTRICT_PATH = 'downloadable';

    public function resolvePath(DocumentTypeInterface $documentType): string
    {
        return $documentType->getVisibility() === Visibility::VISIBILITY_RESTRICT ? self::RESTRICT_PATH : '';
    }
}
