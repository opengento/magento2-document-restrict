<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model;

use Exception;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Opengento\Document\Api\Data\DocumentInterface;
use Opengento\Document\Api\DocumentRepositoryInterface;
use Opengento\Document\Model\Document\Filesystem\File;
use Opengento\Document\Model\ResourceModel\DocumentType\Collection as DocTypeCollection;
use Opengento\Document\Model\ResourceModel\DocumentType\CollectionFactory as DocTypeCollectionFactory;
use Opengento\DocumentRestrict\Model\ResourceModel\Migrate as MigrateDb;
use Opengento\Document\Model\ResourceModel\Document\Collection as DocumentCollection;
use Opengento\Document\Model\ResourceModel\Document\CollectionFactory as DocumentCollectionFactory;
use Psr\Log\LoggerInterface;
use function array_diff;
use function basename;
use function dirname;

final class Migrate
{
    /**
     * @var MigrateDb
     */
    private $migrateDb;

    /**
     * @var DocumentCollectionFactory
     */
    private $documentCollectionFactory;

    /**
     * @var DocTypeCollectionFactory
     */
    private $docTypeCollectionFactory;

    /**
     * @var DocumentRepositoryInterface
     */
    private $documentRepository;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var File
     */
    private $file;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        MigrateDb $migrateDb,
        DocumentCollectionFactory $documentCollectionFactory,
        DocTypeCollectionFactory $docTypeCollectionFactory,
        DocumentRepositoryInterface $documentRepository,
        HydratorPool $hydratorPool,
        File $file,
        LoggerInterface $logger
    ) {
        $this->migrateDb = $migrateDb;
        $this->documentCollectionFactory = $documentCollectionFactory;
        $this->docTypeCollectionFactory = $docTypeCollectionFactory;
        $this->documentRepository = $documentRepository;
        $this->hydrator = $hydratorPool->getHydrator(DocumentInterface::class);
        $this->file = $file;
        $this->logger = $logger;
    }

    /**
     * @throws FileSystemException
     */
    public function migrateQueue(): void
    {
        $typeIds = $this->migrateDb->fetchQueuedTypeIds();
        $failedTypeIds = [];

        // Start migration
        $this->migrateDb->updateRunningState($typeIds);

        // Todo: handle batch management to avoid out of memory on large dataset with tons of files
        $documentCollection = $this->createDocumentCollection($typeIds);
        $docTypeCollection = $this->createDocTypeCollection($typeIds);

        foreach ($documentCollection->getItems() as $document) {
            $filePath = $this->file->getFilePath($document);
            $destPath = $this->file->getFileDestPath(
                $docTypeCollection->getItemById($document->getTypeId()),
                $filePath
            );

            try {
                $this->file->moveFile($filePath, $destPath);
                $this->documentRepository->save($this->updateDocument($document, $destPath));
            } catch (CouldNotSaveException $e) {
                $this->logger->error($e->getPrevious()->getMessage(), $e->getTrace());
                $this->file->moveFile($destPath, $filePath);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
                $failedTypeIds[$document->getTypeId()] = $document->getTypeId();
            }
        }

        // End migration
        $this->migrateDb->updateCompleteState(array_diff($typeIds, $failedTypeIds));
        $this->migrateDb->updateFailureState($failedTypeIds);
    }

    private function updateDocument(DocumentInterface $document, string $filePath): DocumentInterface
    {
        return $this->hydrator->hydrate(
            $document,
            [
                'file_path' => dirname($this->file->getRelativeFilePath($filePath)),
                'file_name' => basename($filePath)
            ]
        );
    }

    private function createDocumentCollection(array $typeIds): DocumentCollection
    {
        $documentCollection = $this->documentCollectionFactory->create();
        $documentCollection->addFieldToFilter('type_id', ['in' => $typeIds]);

        return $documentCollection;
    }

    private function createDocTypeCollection(array $typeIds): DocTypeCollection
    {
        $docTypeCollection = $this->docTypeCollectionFactory->create();
        $docTypeCollection->addFieldToSelect(['entity_id', 'file_dest_path', 'sub_path_length', 'is_restricted']);
        $docTypeCollection->addFieldToFilter('entity_id', ['in' => $typeIds]);

        return $docTypeCollection;
    }
}
