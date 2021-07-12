<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Controller\Adminhtml\Restrict;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Opengento\DocumentRestrict\Api\Data\RestrictInterface;
use Opengento\DocumentRestrict\Api\RestrictRepositoryInterface;
use Opengento\DocumentRestrict\Model\ResourceModel\Document\CollectionFactory;

class MassDelete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_DocumentRestrict::document_restrict_delete';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var RestrictRepositoryInterface
     */
    private $restrictRepository;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        RestrictRepositoryInterface $restrictRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->restrictRepository = $restrictRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        try {
            $this->massAction($this->filter->getCollection($this->collectionFactory->create()));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        return $resultRedirect;
    }

    /**
     * @param AbstractDb $collection
     * @return void
     * @throws CouldNotDeleteException
     */
    private function massAction(AbstractDb $collection): void
    {
        /** @var RestrictInterface $restrict */
        foreach ($collection->getItems() as $restrict) {
            $this->restrictRepository->delete($restrict);
        }

        $this->messageManager->addSuccessMessage(
            new Phrase('A total of %1 record(s) has been deleted.', [$collection->count()])
        );
    }
}
