<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Controller\Restrict;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Url\DecoderInterface;
use Opengento\DocumentRestrict\Api\AuthenticationInterface;
use Opengento\DocumentRestrict\Model\AuthSession;

class Auth implements HttpPostActionInterface
{
    private const PARAM_NAME_TYPE_ID = 'typeId';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var AuthSession
     */
    private $authSession;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    public function __construct(
        RequestInterface $request,
        DecoderInterface $decoder,
        RedirectFactory $redirectFactory,
        AuthSession $authSession,
        AuthenticationInterface $authentication,
        ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->decoder = $decoder;
        $this->redirectFactory = $redirectFactory;
        $this->authSession = $authSession;
        $this->authentication = $authentication;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $redirectResult = $this->redirectFactory->create();
        $authRequest = $this->authSession->getAuthRequest();

        if ($authRequest !== null &&
            $this->authentication->authenticate($this->request->getParam(self::PARAM_NAME_TYPE_ID, -1), $authRequest)
        ) {
            $redirectResult->setUrl($this->decoder->decode($this->request->getParam(self::PARAM_NAME_URL_ENCODED)));
        } else {
            $redirectResult->setRefererOrBaseUrl();
            $this->messageManager->addErrorMessage(new Phrase('Invalid credentials.'));
        }

        return $redirectResult;
    }
}
