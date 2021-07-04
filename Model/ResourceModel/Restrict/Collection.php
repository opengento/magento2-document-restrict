<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\DocumentRestrict\Model\ResourceModel\Restrict;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Opengento\DocumentRestrict\Model\ResourceModel\Restrict as ResourceModel;
use Opengento\DocumentRestrict\Model\Restrict as Model;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
