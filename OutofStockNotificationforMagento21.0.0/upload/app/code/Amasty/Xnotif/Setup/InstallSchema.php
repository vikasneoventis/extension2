<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
namespace Amasty\Xnotif\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup,
                            ModuleContextInterface $context
    )
    {
        $installer = $setup;

        $installer->startSetup();
        $config = $installer->getConnection()->getConfig();
        $dbname = $config['dbname'];
        $tableName = $installer->getConnection()->getTableName(
            'product_alert_stock'
        );

        $fieldsSql = 'SHOW COLUMNS FROM ' . $tableName;
        $cols = $installer->getConnection()->fetchCol($fieldsSql);


        if (!in_array('parent_id', $cols)) {
            $installer->getConnection()->query(
                "
        ALTER TABLE `{$tableName}` ADD COLUMN `parent_id` INT NULL;
    "
            );
        }

        if (!in_array('email', $cols)) {
            $installer->getConnection()->query(
                "
        ALTER TABLE `{$tableName}` ADD COLUMN `email` VARCHAR( 254 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
    "
            );
        }

        $sql
            = "select * from     information_schema.key_column_usage where table_name='{$tableName}' and column_name='customer_id' and table_schema='{$dbname}';";
        $keys = $installer->getConnection()->rawQuery($sql)->fetchAll();
        foreach ($keys as $keyName) {
            $installer->getConnection()->query(
                "
                    ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$keyName['CONSTRAINT_NAME']}`;
                "
            );
        }
        $sql = 'SHOW INDEX FROM ' . $tableName
            . " where column_name = 'customer_id'";
        $keys = $installer->getConnection()->rawQuery($sql)->fetchAll();
        foreach ($keys as $keyName) {
            $installer->getConnection()->query(
                "
                    ALTER TABLE `{$tableName}` DROP INDEX `{$keyName['Key_name']}`;
                "
            );
        }


        $tableName = $installer->getConnection()->getTableName(
            'product_alert_price'
        );
        $fieldsSql = 'SHOW COLUMNS FROM ' . $tableName;
        $cols = $installer->getConnection()->fetchCol($fieldsSql);

        if (!in_array('parent_id', $cols)) {
            $installer->getConnection()->query(
                "
            ALTER TABLE `{$tableName}` ADD COLUMN `parent_id` INT NULL;
        "
            );
        }

        if (!in_array('email', $cols)) {
            $installer->getConnection()->query(
                "
            ALTER TABLE `{$tableName}` ADD COLUMN `email` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
        "
            );
        }

        $sql
            = "select * from     information_schema.key_column_usage where table_name='{$tableName}' and column_name='customer_id' and table_schema='{$dbname}';";
        $keys = $installer->getConnection()->rawQuery($sql)->fetchAll();
        foreach ($keys as $keyName) {
            $installer->getConnection()->query(
                "
                    ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$keyName['CONSTRAINT_NAME']}`;
                "
            );
        }
        $sql = 'SHOW INDEX FROM ' . $tableName
            . " where column_name = 'customer_id'";
        $keys = $installer->getConnection()->rawQuery($sql)->fetchAll();
        foreach ($keys as $keyName) {
            $installer->getConnection()->query(
                "
                    ALTER TABLE `{$tableName}` DROP INDEX `{$keyName['Key_name']}`;
                "
            );
        }

        $installer->endSetup();
    }
}