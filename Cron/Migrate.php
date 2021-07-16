<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Cron;

use Magento\Framework\Exception\FileSystemException;
use Opengento\DocumentRestrict\Model\Migrate as MigrateService;
use Psr\Log\LoggerInterface;

final class Migrate
{
    /**
     * @var MigrateService
     */
    private $migrateService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        MigrateService $migrateService,
        LoggerInterface $logger
    ) {
        $this->migrateService = $migrateService;
        $this->logger = $logger;
    }

    public function execute(): void
    {
        try {
            $this->migrateService->migrateQueue();
        } catch (FileSystemException $e) {
            $this->logger->error($e->getLogMessage(), $e->getTrace());
        }
    }
}
