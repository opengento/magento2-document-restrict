<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model;

use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\EntityManager\HydratorPool;
use Opengento\DocumentRestrict\Api\AuthenticationInterface;
use Opengento\DocumentRestrict\Api\Data\AuthRequestInterface;
use Opengento\DocumentRestrict\Api\Data\RestrictInterface;
use Opengento\DocumentRestrict\Model\ResourceModel\Restrict\Collection;
use Opengento\DocumentRestrict\Model\ResourceModel\Restrict\CollectionFactory;
use function crypt;
use function hash;

final class Authentication implements AuthenticationInterface
{
    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        HydratorPool $hydratorPool,
        CollectionFactory $collectionFactory
    ) {
        $this->hydrator = $hydratorPool->getHydrator(RestrictInterface::class);
        $this->collectionFactory = $collectionFactory;
    }

    public function authenticate(int $typeId, AuthRequestInterface $authRequest): bool
    {
        $privateSecret = $authRequest->getPrivateSecret();

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('type_id', $typeId);
        $collection->addFieldToFilter('public_secret', $authRequest->getPublicSecret());
        $collection->addFieldToFilter(
            'private_secret',
            $privateSecret ? $this->hashSecret($privateSecret) : [['null' => true], ['eq' => '']]
        );

        return (bool) $collection->getSize();
    }

    public function setPrivateSecret(RestrictInterface $restrict, string $privateSecret): RestrictInterface
    {
        /** @var RestrictInterface $hydratedRestrict */
        $hydratedRestrict = $this->hydrator->hydrate(
            $restrict,
            ['private_secret' => $this->hashSecret($privateSecret)]
        );

        return $hydratedRestrict;
    }

    private function hashSecret(string $secret): string
    {
        return crypt($secret, hash('sha256', $secret));
    }
}
