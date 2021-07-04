<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model;

use DateTime;
use Exception;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Opengento\Document\Model\DocumentType;
use Opengento\DocumentRestrict\Api\Data\RestrictExtensionInterface;
use Opengento\DocumentRestrict\Api\Data\RestrictInterface;
use Opengento\DocumentRestrict\Model\ResourceModel\Restrict as ResourceModel;

class Restrict extends AbstractExtensibleModel implements RestrictInterface, IdentityInterface
{
    public const CACHE_TAG = 'ope_dtr';

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    public function getIdentities(): array
    {
        return [
            self::CACHE_TAG,
            self::CACHE_TAG . '_' . $this->getId(),
            DocumentType::CACHE_TAG . '_' . $this->getTypeId(),
        ];
    }

    public function getCacheTags(): array
    {
        return $this->getIdentities() ?: parent::getCacheTags();
    }

    public function getId(): ?int
    {
        return parent::getId() ? (int) parent::getId() : null;
    }

    public function getTypeId(): int
    {
        return (int) $this->_getData('type_id');
    }

    public function getPublicSecret(): ?string
    {
        return $this->_getData('public_secret') ? (string) $this->_getData('public_secret') : null;
    }

    public function getPrivateSecret(): ?string
    {
        return $this->_getData('private_secret') ? (string) $this->_getData('private_secret') : null;
    }

    public function getCustomerId(): ?int
    {
        return $this->_getData('customer_id') ? (int) $this->_getData('customer_id') : null;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getCreatedAt(): DateTime
    {
        return new DateTime($this->_getData('created_at'));
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getUpdatedAt(): DateTime
    {
        return new DateTime($this->_getData('updated_at'));
    }

    public function getExtensionAttributes(): RestrictExtensionInterface
    {
        if (!$this->_getExtensionAttributes()) {
            $this->setExtensionAttributes($this->extensionAttributesFactory->create(RestrictInterface::class));
        }

        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(RestrictExtensionInterface $extensionAttributes): RestrictInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
