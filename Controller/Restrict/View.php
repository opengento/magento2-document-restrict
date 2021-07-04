<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Controller\Restrict;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Opengento\Document\Api\DocumentRepositoryInterface;
use Opengento\DocumentRestrict\App\Response\DocumentFactory;
use Opengento\DocumentRestrict\Exception\EmptyAuthException;
use Opengento\DocumentRestrict\Exception\InvalidAuthException;
use Psr\Log\LoggerInterface;

class View implements HttpGetActionInterface
{
    public const HTTP_PARAM_DOCUMENT_ID = 'id';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DocumentRepositoryInterface
     */
    private $documentRepository;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var documentFactory
     */
    private $documentFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        RequestInterface $request,
        DocumentRepositoryInterface $documentRepository,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        DocumentFactory $documentFactory,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->documentRepository = $documentRepository;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->documentFactory = $documentFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $response = $this->documentFactory->create(
                $this->documentRepository->getById((int) $this->request->getParam(self::HTTP_PARAM_DOCUMENT_ID)),
                $this->request
            );
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());
            $response = $this->forwardNoRoute();
        } catch (EmptyAuthException $e) {
            $response = $this->redirectFailAuth();
        } catch (InvalidAuthException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $response = $this->redirectFailAuth();
        } catch (FileSystemException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An unexpected error occurred on the server.'));
        }

        return $response ?? $this->forwardNoRoute();
    }

    private function redirectFailAuth(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererOrBaseUrl();
    }

    private function forwardNoRoute(): ResultInterface
    {
        /** @var Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $resultForward->forward('no_route');
    }
}
