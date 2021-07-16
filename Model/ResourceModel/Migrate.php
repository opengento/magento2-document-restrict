<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model\ResourceModel;

use DateTime;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Migrate extends AbstractDb
{
    public const STATE_PENDING = 'pending';
    public const STATE_RUNNING = 'running';
    public const STATE_COMPLETE = 'complete';
    public const STATE_FAILURE = 'failure';

    protected function _construct(): void
    {
        $this->_init('opengento_document_type_restrict_migrate', 'entity_id');
    }

    public function scheduleMigration(int $typeId): void
    {
        $this->getConnection()->insert($this->getMainTable(), ['state' => self::STATE_PENDING, 'type_id' => $typeId]);
    }

    public function fetchQueuedTypeIds(): array
    {
        $select = $this->getConnection()->select();
        $select->from($this->getMainTable(), 'type_id');
        $select->where('state IN (?)', [self::STATE_PENDING, self::STATE_FAILURE]);

        return $this->getConnection()->fetchCol($select);
    }

    public function updateRunningState(array $typeIds): void
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            ['state' => self::STATE_RUNNING],
            ['type_id IN (?)' => $typeIds, 'state IN (?)' => [self::STATE_PENDING, self::STATE_FAILURE]]
        );
    }

    public function updateCompleteState(array $typeIds): void
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            ['state' => self::STATE_COMPLETE],
            ['type_id IN (?)' => $typeIds, 'state = ?' => self::STATE_RUNNING]
        );
    }

    public function updateFailureState(array $typeIds): void
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            ['state' => self::STATE_FAILURE],
            ['type_id IN (?)' => $typeIds, 'state = ?' => self::STATE_RUNNING]
        );
    }

    public function deleteOlderThan(DateTime $dateTime): void
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['state = ?' => self::STATE_COMPLETE, 'updated_at <= ?' => $dateTime]
        );
    }
}
