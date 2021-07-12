<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model\Document\Filesystem\UrlResolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Opengento\Document\Api\Data\DocumentInterface;
use Opengento\Document\Api\DocumentTypeRepositoryInterface;
use Opengento\Document\Model\Document\Filesystem\UrlResolverInterface;
use Opengento\DocumentRestrict\Model\DocumentType\Visibility;
use Psr\Log\LoggerInterface;

final class VisibilityResolver implements UrlResolverInterface
{
    /**
     * @var DocumentTypeRepositoryInterface
     */
    private $documentTypeRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        DocumentTypeRepositoryInterface $documentTypeRepository,
        UrlInterface $urlBuilder,
        LoggerInterface $logger
    ) {
        $this->documentTypeRepository = $documentTypeRepository;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
    }

    public function getUrl(DocumentInterface $document): ?string
    {
        try {
            $documentType = $this->documentTypeRepository->getById($document->getTypeId());
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());
            $documentType = null;
        }

        return $documentType && $documentType->getVisibility() === Visibility::VISIBILITY_RESTRICT
            ? $this->urlBuilder->getUrl('document/restrict/view', ['id' => $document->getId()])
            : null;
    }
}
