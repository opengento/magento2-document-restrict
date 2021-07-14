<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Block\Widget\DocumentType;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Opengento\Document\Api\Data\DocumentTypeInterface;
use Opengento\Document\Api\DocumentTypeRepositoryInterface;

class Auth extends Template implements BlockInterface, IdentityInterface
{
    /**
     * @var DocumentTypeRepositoryInterface
     */
    private $docTypeRepository;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    public function __construct(
        Context $context,
        DocumentTypeRepositoryInterface $docTypeRepository,
        EncoderInterface $encoder,
        array $data = []
    ) {
        $this->docTypeRepository = $docTypeRepository;
        $this->encoder = $encoder;
        parent::__construct($context, $data);
    }

    public function getIdentities(): array
    {
        $documentType = $this->resolveDocumentType();

        return $documentType instanceof IdentityInterface ? $documentType->getIdentities() : [];
    }

    protected function _beforeToHtml(): Auth
    {
        if (!$this->hasData('post_action_url')) {
            $this->setData('post_action_url', $this->getUrl('document/restrict/auth'));
        }
        if ($this->hasData('url')) {
            $this->setData('uenc', $this->encoder->encode($this->getUrl($this->getData('url'))));
        }

        return parent::_beforeToHtml();
    }

    protected function _toHtml(): string
    {
        return $this->resolveDocumentType() ? parent::_toHtml() : '';
    }

    private function resolveDocumentType(): ?DocumentTypeInterface
    {

        if (!$this->hasData('documentType')) {
            try {
                $this->setData('documentType', $this->docTypeRepository->getById((int) $this->getData('type_id')));
            } catch (NoSuchEntityException $e) {
                $this->_logger->error($e->getLogMessage(), $e->getTrace());
                $this->setData('documentType');
            }
        }

        return $this->_getData('documentType');
    }
}
