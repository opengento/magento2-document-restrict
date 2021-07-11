<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\DocumentRestrict\Api\RestrictRepositoryInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_DocumentRestrict::document_restrict_delete';

    /**
     * @var RestrictRepositoryInterface
     */
    private $restrictRepository;

    public function __construct(
        Context $context,
        RestrictRepositoryInterface $restrictRepository
    ) {
        $this->restrictRepository = $restrictRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $this->restrictRepository->delete(
                $this->restrictRepository->getById((int) $this->getRequest()->getParam('id'))
            );
            $this->messageManager->addSuccessMessage(
                new Phrase('The document restriction has been successfully deleted.')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
