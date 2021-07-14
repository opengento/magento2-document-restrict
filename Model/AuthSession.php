<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Session\SessionStartChecker;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Session\StorageInterface;
use Magento\Framework\Session\ValidatorInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Opengento\DocumentRestrict\Api\Data\AuthRequestInterface;

class AuthSession extends SessionManager
{
    /**
     * @var AuthRequestBuilder
     */
    private $authRequestBuilder;

    public function __construct(
        Http $request,
        SidResolverInterface $sidResolver,
        ConfigInterface $sessionConfig,
        SaveHandlerInterface $saveHandler,
        ValidatorInterface $validator,
        StorageInterface $storage,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        State $appState,
        SessionStartChecker $sessionStartChecker,
        AuthRequestBuilder $authRequestBuilder
    ) {
        $this->authRequestBuilder = $authRequestBuilder;
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState,
            $sessionStartChecker
        );
    }

    public function setAuthRequest(AuthRequestInterface $authRequest): void
    {
        $this->set('authRequest', $authRequest);
    }

    public function getAuthRequest(): ?AuthRequestInterface
    {
        $authRequest = $this->resolveAuthRequest() ?: $this->getData('authRequest');

        return $authRequest instanceof AuthRequestInterface ? $authRequest : null;
    }

    private function resolveAuthRequest(): ?AuthRequestInterface
    {
        $authRequest = null;

        if ($this->request->isPost()) {
            try {
                $authRequest = $this->authRequestBuilder->createFromRequest($this->request);
            } catch (InputException $e) {
                // Silence is golden
            }
        }

        return $authRequest;
    }
}
