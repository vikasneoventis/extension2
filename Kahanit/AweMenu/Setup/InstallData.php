<?php

namespace Kahanit\AweMenu\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    protected $jsonDecoder;

    protected $storeManager;

    protected $moduleReader;

    protected $aweMenuModel;

    protected $aweMenuHelper;

    public function __construct(
        \Magento\Framework\Json\Decoder $jsonDecoder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Kahanit\AweMenu\Model\ResourceModel\AweMenu $aweMenuModel,
        \Kahanit\AweMenu\Helper\AweMenu $aweMenuHelper
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->storeManager = $storeManager;
        $this->moduleReader = $moduleReader;
        $this->aweMenuModel = $aweMenuModel;
        $this->aweMenuHelper = $aweMenuHelper;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $menutxt = file_get_contents($this->moduleReader->getModuleDir('', 'Kahanit_AweMenu') . '/Setup/menu.txt');
        $themetxt = file_get_contents($this->moduleReader->getModuleDir('', 'Kahanit_AweMenu') . '/Setup/theme.txt');

        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $menu = [
                'title' => 'Main Menu',
                'shop' => $store->getId(),
                'author' => '0',
                'menu' => $this->jsonDecoder->decode($this->aweMenuHelper->stripslashes($menutxt)),
                'theme' => $this->jsonDecoder->decode($this->aweMenuHelper->stripslashes($themetxt)),
                'edit' => '1',
                'live' => '1',
                'deleted' => '0',
                'date' => time()
            ];
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->aweMenuModel->saveMenu($objectManager->create('Kahanit\AweMenu\Model\AweMenu'), $store->getId(), $menu);
        }
    }
}
