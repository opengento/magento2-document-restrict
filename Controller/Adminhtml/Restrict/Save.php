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
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Opengento\DocumentRestrict\Api\AuthenticationInterface;
use Opengento\DocumentRestrict\Api\Data\RestrictInterface;
use Opengento\DocumentRestrict\Api\Data\RestrictInterfaceFactory;
use Opengento\DocumentRestrict\Api\RestrictRepositoryInterface;
use function array_filter;
use function array_intersect_key;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_DocumentRestrict::document_restrict_save';

    /**
     * @var RestrictRepositoryInterface
     */
    private $restrictRepository;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var RestrictInterfaceFactory
     */
    private $restrictFactory;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var string[]
     */
    private $allowedFields;

    public function __construct(
        Context $context,
        RestrictRepositoryInterface $restrictRepository,
        AuthenticationInterface $authentication,
        RestrictInterfaceFactory $restrictFactory,
        DataPersistorInterface $dataPersistor,
        HydratorPool $hydratorPool,
        array $allowedFields
    ) {
        $this->restrictRepository = $restrictRepository;
        $this->authentication = $authentication;
        $this->restrictFactory = $restrictFactory;
        $this->dataPersistor = $dataPersistor;
        $this->hydrator = $hydratorPool->getHydrator(RestrictInterface::class);
        $this->allowedFields = $allowedFields;
        parent::__construct($context);
    }

    public function execute()
    {
        $entityId = (int) $this->getRequest()->getParam('entity_id');
        $this->dataPersistor->set('document_restrict_post_data', $this->getRequest()->getParams());

        try {
            $entityId = $this->restrictRepository->save($this->resolveRestrict())->getId();
            $this->dataPersistor->clear('document_restrict_post_data');
            $this->messageManager->addSuccessMessage(
                new Phrase('The restriction has been successfully saved.')
            );
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage($e->getPrevious()->getMessage());
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');

        if ($this->dataPersistor->get('document_restrict_post_data') || $this->getRequest()->getParam('back')) {
            $resultRedirect->setPath('*/*/edit', ['id' => $entityId]);
        }

        return $resultRedirect;
    }

    /**
     * @return RestrictInterface
     * @throws NoSuchEntityException
     */
    private function resolveRestrict(): RestrictInterface
    {
        $entityId = (int) $this->getRequest()->getParam('entity_id');
        $restrictData = array_intersect_key($this->getRequest()->getParams(), array_filter($this->allowedFields));

        /** @var RestrictInterface $restrict */
        $restrict = $this->hydrator->hydrate(
            $entityId ? $this->restrictRepository->getById($entityId) : $this->restrictFactory->create(),
            $restrictData
        );

        $privateSecret = $this->getRequest()->getParam('private_secret');
        if ($privateSecret && $privateSecret !== $restrict->getPrivateSecret()) {
            $restrict = $this->authentication->setPrivateSecret($restrict, $privateSecret);
        }

        return $restrict;
    }
}
