<?php

namespace Kahanit\AweMenu\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('awemenu'))
            ->addColumn('id', Table::TYPE_BIGINT, 20, ['nullable' => false, 'unsigned' => true, 'primary' => true, 'identity' => true])
            ->addColumn('title', Table::TYPE_TEXT, null, ['nullable' => false, 'default' => null])
            ->addColumn('shop', Table::TYPE_INTEGER, 10, ['nullable' => false, 'default' => 0, 'unsigned' => true])
            ->addColumn('author', Table::TYPE_BIGINT, 20, ['nullable' => false, 'default' => 0, 'unsigned' => true])
            ->addColumn('menu', Table::TYPE_TEXT, null, ['nullable' => false, 'default' => null])
            ->addColumn('theme', Table::TYPE_TEXT, null, ['nullable' => false, 'default' => null])
            ->addColumn('edit', Table::TYPE_SMALLINT, 5, ['nullable' => false, 'default' => 0, 'unsigned' => true])
            ->addColumn('live', Table::TYPE_SMALLINT, 5, ['nullable' => false, 'default' => 0, 'unsigned' => true])
            ->addColumn('deleted', Table::TYPE_SMALLINT, 5, ['nullable' => false, 'default' => 0, 'unsigned' => true])
            ->addColumn('date', Table::TYPE_INTEGER, 10, ['nullable' => false, 'default' => 0, 'unsigned' => true]);

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
