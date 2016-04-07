<?php

namespace Kahanit\AweMenu\Block;

use Magento\Framework\Module\Dir;

class AweMenuAdmin extends \Magento\Framework\View\Element\Template
{
    protected $jsonEncoder;

    protected $formKey;

    protected $aweMenuModel;

    public $storeId;

    public $themeId;

    public $getStoreViewDDItems;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Encoder $jsonEncoder,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Kahanit\AweMenu\Model\ResourceModel\AweMenu $aweMenuModel
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->formKey = $formKey;
        $this->aweMenuModel = $aweMenuModel;

        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->getStoreViewDDItems = $this->getStoreViewDDItems();
        $this->storeId = $this->getRequest()->getParam('store', false);
        if ($this->storeId === false) {
            $this->storeId = key($this->getStoreViewDDItems);
        }
        $this->getStoreViewDDItems[$this->storeId]['selected'] = 1;

        $menuId = $this->aweMenuModel->getEditorMenuId($this->storeId);
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $page = $om->get('Magento\Framework\View\Page\Config');

        $page->addPageAsset('Kahanit_AweMenu::css/bootstrap.css');

        $page->addPageAsset('Kahanit_AweMenu::css/jquery.datatables.css');
        $page->addPageAsset('Kahanit_AweMenu::css/jquery.fancytree.css');

        $page->addPageAsset('Kahanit_AweMenu::css/bootstrap-colorpicker.css');
        $page->addPageAsset('Kahanit_AweMenu::css/fontawesome.css');
        $page->addPageAsset('Kahanit_AweMenu::css/fontawesome-iconpicker.css');

        $page->addPageAsset('Kahanit_AweMenu::css/menu-layout.css');
        $page->addPageAsset('Kahanit_AweMenu::css/menu-responsive.css');
        $page->addPageAsset('Kahanit_AweMenu::css/menu-editor.css');
        if ($menuId !== false) {
            $page->addPageAsset('Kahanit_AweMenu::css/menu-theme-' . $menuId . '.css');
        }
        $page->addPageAsset('Kahanit_AweMenu::css/admin.css');
    }

    public function getStoreViewDDItems()
    {
        $stores = $this->_storeManager->getStores();

        $items = [];
        foreach ($stores as $store) {
            $storeId = $store->getId();
            $storeName = $store->getName();
            $items[$storeId] = ['name' => $storeName];
        }

        return $items;
    }

    public function aweMenuMageInit()
    {
        return $this->jsonEncoder->encode((object)[
            'aweMenuLoader' => (object)[
                'url' => $this->getUrl('awemenu/index/ajax', [
                    'store' => $this->storeId,
                    'form_key' => $this->formKey->getFormKey()
                ]),
                'jsUrl' => $this->getViewFileUrl('Kahanit_AweMenu/js'),
                'langs' => [
                    (object)['id' => '0', 'name' => 'Neutral', 'iso' => 'und']
                ],
                'activeLang' => '0',
                'entities' => [
                    (object)['entity' => 'product', 'name' => 'Products', 'format' => 'table'],
                    (object)['entity' => 'category', 'name' => 'Categories', 'format' => 'tree'],
                    (object)['entity' => 'cmspage', 'name' => 'CMS Pages', 'format' => 'table']
                ]
            ]
        ]);
    }
}
