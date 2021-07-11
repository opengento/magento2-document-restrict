<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Result\Page;
use Opengento\DocumentRestrict\Api\Data\RestrictInterfaceFactory;
use Opengento\DocumentRestrict\Api\RestrictRepositoryInterface;
use Opengento\DocumentRestrict\Model\Document\RegistryInterface;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Opengento_DocumentRestrict::document_restrict_save';

    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var RestrictRepositoryInterface
     */
    private $restrictRepository;

    /**
     * @var RestrictInterfaceFactory
     */
    private $restrictFactory;

    public function __construct(
        Context $context,
        RegistryInterface $registry,
        RestrictRepositoryInterface $restrictRepository,
        RestrictInterfaceFactory $restrictFactory
    ) {
        $this->registry = $registry;
        $this->restrictRepository = $restrictRepository;
        $this->restrictFactory = $restrictFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $restrict = $this->getRequest()->getParam('id')
                ? $this->restrictRepository->getById((int) $this->getRequest()->getParam('id'))
                : $this->restrictFactory->create();
            $this->registry->set($restrict);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/');

            return $resultRedirect;
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Opengento_DocumentRestrict::document_restrict');
        $resultPage->getConfig()->getTitle()->prepend(
            $restrict->getId() ? $restrict->getName() : new Phrase('New Document Restriction')
        );

        return $resultPage;
    }
}
