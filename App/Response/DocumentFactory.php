<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\App\Response;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File\Mime;
use Magento\Framework\Phrase;
use Opengento\Document\Api\Data\DocumentInterface;
use Opengento\Document\Model\Document\Filesystem\File;
use Opengento\DocumentRestrict\Api\AuthenticationInterface;
use Opengento\DocumentRestrict\Exception\EmptyAuthException;
use Opengento\DocumentRestrict\Exception\InvalidAuthException;
use Opengento\DocumentRestrict\Model\AuthSession;
use function date;
use function flush;
use function sprintf;

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
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        AuthSession $authSession,
        AuthenticationInterface $authentication,
        File $fileHelper,
        Mime $mime,
        ResponseInterface $response,
        Filesystem $filesystem
    ) {
        $this->authSession = $authSession;
        $this->authentication = $authentication;
        $this->fileHelper = $fileHelper;
        $this->mime = $mime;
        $this->response = $response;
        $this->filesystem = $filesystem;
    }

    /**
     * @throws InvalidAuthException
     * @throws EmptyAuthException
     * @throws FileSystemException
     * @throws Exception
     */
    public function create(DocumentInterface $document, bool $download = false): ResponseInterface
    {
        $authRequest = $this->authSession->getAuthRequest();

        if ($authRequest === null) {
            throw new EmptyAuthException(new Phrase('At least one auth parameter is needed.'));
        }
        if (!$this->authentication->authenticate($document->getTypeId(), $authRequest)) {
            throw new InvalidAuthException(new Phrase('Invalid credentials for the requested access.'));
        }

        $documentPath = $this->fileHelper->getFilePath($document);

        return $this->response(
            $document->getFileName(),
            $documentPath,
            DirectoryList::ROOT,
            $this->mime->getMimeType($documentPath),
            $download
        );
    }

    /**
     * @throws FileSystemException
     */
    public function response(
        string $fileName,
        string $filePath,
        string $baseDir,
        string $contentType,
        bool $download
    ): ResponseInterface
    {
        $dir = $this->filesystem->getDirectoryWrite($baseDir);

        if (!$dir->isFile($filePath)) {
            throw new Exception((string) new Phrase('File not found'));
        }
        $this->response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', $dir->stat($filePath)['size'], true)
            ->setHeader(
                'Content-Disposition',
                sprintf('%s; filename="%s"', $download ? 'attachment' : 'inline', $fileName),
                true
            )
            ->setHeader('Last-Modified', date('r'), true);

        $this->response->sendHeaders();
        $stream = $dir->openFile($filePath, 'r');
        while (!$stream->eof()) {
            echo $stream->read(1024);
        }
        $stream->close();
        flush();

        return $this->response;
    }
}
