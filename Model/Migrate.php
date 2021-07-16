<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model;

use Exception;
use Opengento\Document\Model\Document\Filesystem\File;
use Opengento\Document\Model\ResourceModel\DocumentType\Collection as DocTypeCollection;
use Opengento\Document\Model\ResourceModel\DocumentType\CollectionFactory as DocTypeCollectionFactory;
use Opengento\DocumentRestrict\Model\ResourceModel\Migrate as MigrateDb;
use Opengento\Document\Model\ResourceModel\Document\Collection as DocumentCollection;
use Opengento\Document\Model\ResourceModel\Document\CollectionFactory as DocumentCollectionFactory;
use Psr\Log\LoggerInterface;
use function array_diff;

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
        File $file,
        LoggerInterface $logger
    ) {
        $this->migrateDb = $migrateDb;
        $this->documentCollectionFactory = $documentCollectionFactory;
        $this->docTypeCollectionFactory = $docTypeCollectionFactory;
        $this->file = $file;
        $this->logger = $logger;
    }

    public function migrateQueue(): void
    {
        $typeIds = $this->migrateDb->fetchPendingTypeIds();
        $failedTypeIds = [];

        // Start migration
        $this->migrateDb->updateState($typeIds, 'running');

        // Todo: handle batch management to avoid out of memory on large dataset with tons of files
        $documentCollection = $this->createDocumentCollection($typeIds);
        $docTypeCollection = $this->createDocTypeCollection($typeIds);

        foreach ($documentCollection->getItems() as $document) {
            $filePath = $this->file->getFilePath($document);
            try {
                $this->file->moveFile(
                    $filePath,
                    $this->file->getFileDestPath($docTypeCollection->getItemById($document->getTypeId()), $filePath)
                );
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
                $failedTypeIds[$document->getTypeId()] = $document->getTypeId();
            }
        }

        // End migration
        $this->migrateDb->updateState(array_diff($typeIds, $failedTypeIds), 'completed');
    }

    private function createDocumentCollection(array $typeIds): DocumentCollection
    {
        $documentCollection = $this->documentCollectionFactory->create();
        $documentCollection->addFieldToSelect(['type_id', 'file_name', 'file_path']);
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
