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
    protected function _construct(): void
    {
        $this->_init('opengento_document_type_restrict_migrate', 'entity_id');
    }

    public function scheduleMigration(int $typeId): void
    {
        $this->getConnection()->insert($this->getMainTable(), ['state' => 'pending', 'type_id' => $typeId]);
    }

    public function fetchPendingTypeIds(): array
    {
        $select = $this->getConnection()->select();
        $select->from($this->getMainTable(), 'type_id')->where('state = ?', 'pending', 'VARCHAR');

        return $this->getConnection()->fetchCol($select);
    }

    public function updateState(array $typeIds, string $state): void
    {
        $this->getConnection()->update($this->getMainTable(), ['state' => $state], ['type_id IN (?)' => $typeIds]);
    }

    public function deleteOlderThan(DateTime $dateTime): void
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['state = ?' => 'complete', 'updated_at <= ?' => $dateTime]
        );
    }
}
