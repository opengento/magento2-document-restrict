<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\App\Response;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File\Mime;
use Magento\Framework\Phrase;
use Opengento\Document\Api\Data\DocumentInterface;
use Opengento\Document\Model\Document\Filesystem\File;
use Opengento\DocumentRestrict\Api\AuthenticationInterface;
use Opengento\DocumentRestrict\Exception\EmptyAuthException;
use Opengento\DocumentRestrict\Exception\InvalidAuthException;
use Opengento\DocumentRestrict\Model\AuthSession;

final class DocumentFactory
{
    /**
     * @var AuthSession
     */
    private $authSession;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var File
     */
    private $fileHelper;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        AuthSession $authSession,
        AuthenticationInterface $authentication,
        File $fileHelper,
        Mime $mime,
        FileFactory $fileFactory
    ) {
        $this->authSession = $authSession;
        $this->authentication = $authentication;
        $this->fileHelper = $fileHelper;
        $this->mime = $mime;
        $this->fileFactory = $fileFactory;
    }

    /**
     * @throws InvalidAuthException
     * @throws EmptyAuthException
     * @throws FileSystemException
     * @throws Exception
     */
    public function create(DocumentInterface $document): ResponseInterface
    {
        $authRequest = $this->authSession->getAuthRequest();
        if ($authRequest === null) {
            throw new EmptyAuthException(new Phrase('At least one auth parameter is needed.'));
        }
        if (!$this->authentication->authenticate($document, $authRequest)) {
            throw new InvalidAuthException(new Phrase('Authentication failed! Please try again.'));
        }

        $documentPath = $this->fileHelper->getFilePath($document);

        return $this->fileFactory->create(
            $document->getFileName(),
            ['type' => 'filename', 'value' => $documentPath],
            DirectoryList::ROOT,
            $this->mime->getMimeType($documentPath)
        );
    }
}
