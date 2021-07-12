<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model;

use Magento\Framework\Encryption\EncryptorInterface;
use Opengento\Document\Api\Data\DocumentInterface;
use Opengento\DocumentRestrict\Api\AuthenticationInterface;
use Opengento\DocumentRestrict\Api\Data\AuthRequestInterface;
use Opengento\DocumentRestrict\Model\ResourceModel\Restrict\Collection;
use Opengento\DocumentRestrict\Model\ResourceModel\Restrict\CollectionFactory;

final class Authentication implements AuthenticationInterface
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        EncryptorInterface $encryptor,
        CollectionFactory $collectionFactory
    ) {
        $this->encryptor = $encryptor;
        $this->collectionFactory = $collectionFactory;
    }

    public function authenticate(int $typeId, AuthRequestInterface $authRequest): bool
    {
        $privateSecret = $authRequest->getPrivateSecret();
        if ($privateSecret) {
            $privateSecret = $this->encryptor->getHash($privateSecret, $this->encryptor->encrypt($privateSecret));
        }

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('type_id', $typeId);
        $collection->addFieldToFilter('public_secret', $authRequest->getPublicSecret());
        $collection->addFieldToFilter('private_secret', $privateSecret);

        return (bool) $collection->getSize();
    }
}
