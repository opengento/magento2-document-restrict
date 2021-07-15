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
    private const PARAM_NAME_TYPE_ID = 'type_id';
    private const PARAM_NAME_REDIRECT_TO = 'redirect_to';

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
        $redirectResult->setRefererOrBaseUrl();
        $authRequest = $this->authSession->getAuthRequest();
        $typeId = (int) $this->request->getParam(self::PARAM_NAME_TYPE_ID, -1);

        if ($authRequest !== null &&
            $this->authentication->authenticate($typeId, $authRequest)
        ) {
            $redirectUrl = $this->request->getParam(self::PARAM_NAME_REDIRECT_TO);
            if ($redirectUrl) {
                $redirectResult->setUrl($this->decoder->decode($redirectUrl));
            }
        } else {
            $this->messageManager->addErrorMessage(new Phrase('Invalid credentials for the requested access.'));
        }

        return $redirectResult;
    }
}
