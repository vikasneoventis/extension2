<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Amasty\Xnotif\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup,
                            ModuleContextInterface $context
    )
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'amxnotif_hide_alert',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Hide Stock Alert Block',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'bundle'
            ]
        );
        $attributeId = $eavSetup->getAttributeId(
            \Magento\Catalog\Model\Product::ENTITY,
            'amxnotif_hide_alert'
        );

        foreach (
            $eavSetup->getAllAttributeSetIds(
                \Magento\Catalog\Model\Product::ENTITY
            ) as $attributeSetId
        ) {
            try {
                $attributeGroupId = $eavSetup->getAttributeGroupId(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attributeSetId,
                    'General'
                );
            } catch (Exception $e) {
                $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attributeSetId
                );
            }
            $eavSetup->addAttributeToSet(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeSetId,
                $attributeGroupId,
                $attributeId
            );
        }

        $installer = $setup;
        $tableName = $installer->getConnection()->getTableName(
            'core_config_data'
        );
        $fieldsSql = 'SELECT * FROM ' . $tableName
            . " WHERE `path` like 'catalog/productalert/allow_stock'";
        $cols = $installer->getConnection()->fetchCol($fieldsSql);
        if ($cols) {
            $installer->getConnection()->query(
                "
                UPDATE `{$tableName}` SET `value` = '1' WHERE `path` = 'catalog/productalert/allow_stock';
            "
            );
        } else {
            $installer->getConnection()->query(
                "
            INSERT into `{$tableName}`(`scope`, `scope_id`, `path`, `value`) VALUES ('default', 0, 'catalog/productalert/allow_stock', '1');
        "
            );
        }

    }
}
