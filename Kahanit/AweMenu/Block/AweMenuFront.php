<?php

namespace Kahanit\AweMenu\Block;

class AweMenuFront extends \Magento\Framework\View\Element\Template implements \Magento\Framework\DataObject\IdentityInterface
{
    protected $jsonDecoder;

    protected $aweMenuModel;

    protected $productFactory;

    protected $categoryFactory;

    protected $pageFactory;

    protected $pageHelper;

    public $storeId;

    public $css_styles = array();

    public $css_styles_counter = 1;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Decoder $jsonDecoder,
        \Kahanit\AweMenu\Model\ResourceModel\AweMenu $aweMenuModel,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Helper\Page $pageHelper,
        array $data = []
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->aweMenuModel = $aweMenuModel;
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->pageFactory = $pageFactory;
        $this->pageHelper = $pageHelper;

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->storeId = $this->_storeManager->getStore()->getId();
        $menuId = $this->aweMenuModel->getLiveMenuId($this->storeId);
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $page = $om->get('Magento\Framework\View\Page\Config');
        $page->addPageAsset('Kahanit_AweMenu::css/fontawesome.css');
        $page->addPageAsset('Kahanit_AweMenu::css/menu-layout.css');
        $page->addPageAsset('Kahanit_AweMenu::css/menu-responsive.css');
        if ($menuId !== false) {
            $page->addPageAsset('Kahanit_AweMenu::css/menu-theme-' . $menuId . '.css');
            $page->addPageAsset('Kahanit_AweMenu::css/menu-custom-' . $menuId . '.css');
        }

        $this->addData(array(
            'cache_lifetime' => false,
            'cache_tags' => [
                \Magento\Store\Model\Store::CACHE_TAG,
                \Magento\Catalog\Model\Product::CACHE_TAG,
                \Magento\Catalog\Model\Category::CACHE_TAG,
                \Magento\Cms\Model\Page::CACHE_TAG,
                \Kahanit\AweMenu\Model\AweMenu::CACHE_TAG
            ]
        ));
    }

    public function getCacheKeyInfo()
    {
        return array(
            'KAHANIT_AWEMENU',
            $this->_storeManager->getStore()->getId()
        );
    }

    public function getIdentities()
    {
        return [
            \Magento\Store\Model\Store::CACHE_TAG,
            \Magento\Catalog\Model\Product::CACHE_TAG,
            \Magento\Catalog\Model\Category::CACHE_TAG,
            \Magento\Cms\Model\Page::CACHE_TAG,
            \Kahanit\AweMenu\Model\AweMenu::CACHE_TAG
        ];
    }

    public function generateMenu()
    {
        $menu = $this->aweMenuModel->getLiveMenu($this->storeId);
        $menu['menu'] = $this->jsonDecoder->decode($menu['menu']);
        $menu['theme'] = $this->jsonDecoder->decode($menu['theme']);
        $menu['menu'] = $this->validateMenu($menu['menu']);

        return $menu['menu'];
    }

    public function validateMenu($items = array())
    {
        if (!is_array($items) || count($items) == 0) {
            return false;
        }

        foreach ($items as &$item) {
            if ($item['xtype'] == 'tab' || $item['xtype'] == 'link') {
                switch ($item['config']['entity']) {
                    case 'product':
                        $product = $this->productFactory->create()->load($item['config']['entity_item_id']);
                        $item['config']['url'] = array();

                        if ($product->getId() !== null) {
                            $item['config']['text']['lang_id_0'] = $product->getName();
                            $item['config']['url']['lang_id_0'] = $product->getProductUrl();
                        } else {
                            $item['config']['url']['lang_id_0'] = '#';
                        }

                        break;
                    case 'category':
                        $category = $this->categoryFactory->create()->load($item['config']['entity_item_id']);
                        $item['config']['url'] = array();

                        if ($category->getId() !== null) {
                            $item['config']['text']['lang_id_0'] = $category->getName();
                            $item['config']['url']['lang_id_0'] = $category->getUrl();
                        } else {
                            $item['config']['url']['lang_id_0'] = '#';
                        }

                        break;
                    case 'cmspage':
                        $cmspage = $this->pageFactory->create()->load($item['config']['entity_item_id']);
                        $cmspagehelper = $this->pageHelper;
                        $item['config']['url'] = array();

                        if ($cmspage->getId() !== null) {
                            $item['config']['text']['lang_id_0'] = $cmspage->getTitle();
                            $item['config']['url']['lang_id_0'] = $cmspagehelper->getPageUrl($cmspage->getId());
                        } else {
                            $item['config']['url']['lang_id_0'] = '#';
                        }

                        break;
                }
            }

            $item['items'] = $this->validateMenu($item['items']);
        }

        return $items;
    }
}
