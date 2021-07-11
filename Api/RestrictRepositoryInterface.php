<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\DocumentRestrict\Api\Data\RestrictInterface;
use Opengento\DocumentRestrict\Api\Data\RestrictSearchResultsInterface;

/**
 * @api
 */
interface RestrictRepositoryInterface
{
    /**
     * @param int $RestrictId
     * @return RestrictInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $RestrictId): RestrictInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return RestrictSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param RestrictInterface $Restrict
     * @return RestrictInterface
     * @throws CouldNotSaveException
     */
    public function save(RestrictInterface $Restrict): RestrictInterface;

    /**
     * @param RestrictInterface $Restrict
     * @throws CouldNotDeleteException
     */
    public function delete(RestrictInterface $Restrict): void;
}
