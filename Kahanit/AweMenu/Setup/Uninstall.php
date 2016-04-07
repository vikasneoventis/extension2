<?php

namespace Kahanit\AweMenu\Setup;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $uninstaller = $setup;

        $uninstaller->startSetup();

        $uninstaller->getConnection()->dropTable($uninstaller->getTable('awemenu'));

        $uninstaller->endSetup();
    }
}
