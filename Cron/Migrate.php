<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Cron;

use Opengento\DocumentRestrict\Model\Migrate as MigrateService;

final class Migrate
{
    /**
     * @var MigrateService
     */
    private $migrateService;

    public function __construct(
        MigrateService $migrateService
    ) {
        $this->migrateService = $migrateService;
    }

    public function execute(): void
    {
        $this->migrateService->migrateQueue();
    }
}
