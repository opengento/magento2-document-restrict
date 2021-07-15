<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Cron;

use DateTime;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Opengento\DocumentRestrict\Model\ResourceModel\Migrate as MigrateDb;
use function sprintf;

final class Rotate
{
    private const CONFIG_PATH_MIGRATE_DAYS_ROTATE = 'opengento_document/migrate/days_rotate';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var MigrateDb
     */
    private $migrateDb;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MigrateDb $migrateDb
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->migrateDb = $migrateDb;
    }

    /**
     * @throws Exception
     */
    public function execute(): void
    {
        $this->migrateDb->deleteOlderThan(
            new DateTime(sprintf('-%sday', $this->scopeConfig->getValue(self::CONFIG_PATH_MIGRATE_DAYS_ROTATE)))
        );
    }
}
