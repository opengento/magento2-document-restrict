<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model\Document\Filesystem\UrlResolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\UrlInterface;
use Opengento\Document\Api\Data\DocumentInterface;
use Opengento\Document\Api\Data\DocumentTypeInterface;
use Opengento\Document\Api\DocumentTypeRepositoryInterface;
use Opengento\Document\Model\Document\Filesystem\UrlResolverInterface;
use Psr\Log\LoggerInterface;

final class RestrictedResolver implements UrlResolverInterface
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

    public function getFileUrl(DocumentInterface $document): string
    {
        try {
            $documentType = $this->documentTypeRepository->getById($document->getTypeId());
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());
            $documentType = null;
        }

        return $documentType && $this->isRestricted($documentType)
            ? $this->urlBuilder->getUrl('document/restrict/view', ['id' => $document->getId()])
            : '';
    }

    private function isRestricted(DocumentTypeInterface $documentType): bool
    {
        return (bool) ($documentType instanceof AbstractModel
            ? $documentType->getData('is_restricted')
            : $documentType->getExtensionAttributes()->getIsRestricted());
    }
}
