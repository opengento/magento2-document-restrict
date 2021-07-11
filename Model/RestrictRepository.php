<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model;

use Exception;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Opengento\DocumentRestrict\Api\Data\RestrictInterface;
use Opengento\DocumentRestrict\Api\Data\RestrictInterfaceFactory;
use Opengento\DocumentRestrict\Api\Data\RestrictSearchResultsInterface;
use Opengento\DocumentRestrict\Api\Data\RestrictSearchResultsInterfaceFactory;
use Opengento\DocumentRestrict\Api\RestrictRepositoryInterface;
use Opengento\DocumentRestrict\Model\ResourceModel\Restrict as RestrictDb;
use Opengento\DocumentRestrict\Model\ResourceModel\Restrict\Collection;
use Opengento\DocumentRestrict\Model\ResourceModel\Restrict\CollectionFactory;

final class RestrictRepository implements RestrictRepositoryInterface
{
    /**
     * @var RestrictInterfaceFactory
     */
    private $restrictFactory;

    /**
     * @var RestrictDb
     */
    private $restrictDb;

    /**
     * @var RestrictSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $joinProcessor;

    /**
     * @var CollectionProcessor
     */
    private $collectionProcessor;

    /**
     * @var RestrictInterface[]
     */
    private $instances = [];

    public function __construct(
        RestrictInterfaceFactory $restrictFactory,
        RestrictDb $restrictDb,
        RestrictSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionFactory $collectionFactory,
        JoinProcessorInterface $joinProcessor,
        CollectionProcessor $collectionProcessor
    ) {
        $this->restrictFactory = $restrictFactory;
        $this->restrictDb = $restrictDb;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->joinProcessor = $joinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function getById(int $restrictId): RestrictInterface
    {
        if (!isset($this->instances[$restrictId])) {
            /** @var RestrictInterface|Restrict $restrict */
            $restrict = $this->restrictFactory->create();
            $this->restrictDb->load($restrict, $restrictId);
            if (!$restrict->getId()) {
                throw new NoSuchEntityException(new Phrase('No such entity exists with id "%1".', [$restrictId]));
            }
            $this->instances[$restrictId] = $restrict;
        }

        return $this->instances[$restrictId];
    }

    public function save(RestrictInterface $restrict): RestrictInterface
    {
        try {
            /** @var Restrict $restrict */
            $this->restrictDb->save($restrict);
        } catch (Exception $e) {
            throw new CouldNotSaveException(
                new Phrase('Could not save entity with id "%1".', [$restrict->getId()]),
                $e
            );
        }

        $this->instances[$restrict->getId()] = $restrict;

        return $restrict;
    }

    public function delete(RestrictInterface $restrict): void
    {
        try {
            /** @var Restrict $restrict */
            $this->restrictDb->delete($restrict);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                new Phrase('Could not save entity with id "%1".', [$restrict->getId()]),
                $e
            );
        }

        unset($this->instances[$restrict->getId()]);
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process($collection);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var RestrictSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
