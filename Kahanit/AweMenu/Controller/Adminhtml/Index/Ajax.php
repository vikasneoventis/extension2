<?php

namespace Kahanit\AweMenu\Controller\Adminhtml\Index;

use Magento\Store\Model\Store;

class Ajax extends \Magento\Backend\App\Action
{
    protected $jsonEncoder;

    protected $jsonDecoder;

    protected $rawResult;

    protected $productFactory;

    protected $categoryFactory;

    protected $pageFactory;

    protected $aweMenuModel;

    protected $aweMenuHelper;

    protected $storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Json\Encoder $jsonEncoder,
        \Magento\Framework\Json\Decoder $jsonDecoder,
        \Magento\Framework\Controller\Result\Raw $rawResult,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Kahanit\AweMenu\Model\ResourceModel\AweMenu $aweMenuModel,
        \Kahanit\AweMenu\Helper\AweMenu $aweMenuHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->rawResult = $rawResult;
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->pageFactory = $pageFactory;
        $this->aweMenuModel = $aweMenuModel;
        $this->aweMenuHelper = $aweMenuHelper;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    public function execute()
    {
        $app = $this->getRequest()->getParam('app', false);

        if ($app !== false) {
            $url = explode('/', filter_var(rtrim($app, ' / '), FILTER_SANITIZE_URL));

            if (isset($url[0])) {
                //$module_name = $url[0];
                unset($url[0]);
            }

            if (isset($url[1])) {
                //$controller_name = $url[1];
                unset($url[1]);
            }

            if (isset($url[2])) {
                $method_name = $url[2];
                unset($url[2]);
            }

            $url = array_values($url);
            $params = [];

            foreach ($url as $key => $value) {
                if ($key % 2 == 0) {
                    $params[$value] = $url[$key + 1];
                }
            }

            if (isset($method_name) && !empty($method_name) && method_exists($this, $method_name)) {
                $result = $this->$method_name($params);

                switch ($result['content-type']) {
                    case 'json':
                        $this->rawResult->setHeader('Content-type', 'application/json');
                        $content = $result['json'];
                        break;
                    case 'css':
                        $this->rawResult->setHeader('Content-type', 'text/css');
                        $content = $result['css'];
                        break;
                    default:
                        $content = $result;
                        break;
                }

                return $this->rawResult->setContents($content);
            }
        }

        return '';
    }

    protected function getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', Store::DEFAULT_STORE_ID);

        return $this->storeManager->getStore($storeId);
    }

    /* actions */

    protected function getThemeCssfromLess($params)
    {
        return [
            'content-type' => 'css',
            'css' => $this->aweMenuModel->getThemeCssfromLess($params)
        ];
    }

    protected function getEditorMenu()
    {
        $storeId = $this->getRequest()->getParam('store', false);
        if ($storeId === false) {
            return [
                'content-type' => 'json',
                'json' => 'Store is required'
            ];
        }

        $result = $this->aweMenuModel->getEditorMenu($storeId);
        $result['menu'] = $this->jsonDecoder->decode($result['menu']);
        $result['theme'] = $this->jsonDecoder->decode($result['theme']);

        return [
            'content-type' => 'json',
            'json' => $this->jsonEncoder->encode($result)
        ];
    }

    protected function saveMenu()
    {
        $storeId = $this->getRequest()->getParam('store', false);
        if ($storeId === false) {
            return [
                'content-type' => 'json',
                'json' => 'Store is required'
            ];
        }

        /* save menu */
        $menu = $this->jsonDecoder->decode(urldecode($this->getRequest()->getParam('menu', '{}')));
        $this->aweMenuModel->saveMenu(
            $this->_objectManager->create('Kahanit\AweMenu\Model\AweMenu'),
            $storeId,
            $menu
        );

        return $this->getEditorMenu();
    }

    /* revision functions */

    protected function getRevisions()
    {
        $storeId = $this->getRequest()->getParam('store', false);
        if ($storeId === false) {
            return [
                'content-type' => 'json',
                'json' => 'Store is required'
            ];
        }

        $result = $this->aweMenuModel->getRevisions($storeId);
        $count = $this->aweMenuModel->getNumRevisions($storeId);

        return [
            'content-type' => 'json',
            'json' => $this->jsonEncoder->encode([
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'data' => $result
            ])
        ];
    }

    protected function editRevision($params = [])
    {
        $storeId = $this->getRequest()->getParam('store', false);
        if ($storeId === false) {
            return [
                'content-type' => 'json',
                'json' => 'Store is required'
            ];
        }

        $this->aweMenuModel->editRevision(
            $this->_objectManager->create('Kahanit\AweMenu\Model\AweMenu'),
            $storeId,
            $params['id']
        );

        return $this->getEditorMenu();
    }

    protected function liveRevision($params = [])
    {
        $storeId = $this->getRequest()->getParam('store', false);
        if ($storeId === false) {
            return [
                'content-type' => 'json',
                'json' => 'Store is required'
            ];
        }

        return [
            'content-type' => 'json',
            'json' => $this->jsonEncoder->encode($this->aweMenuModel->liveRevision(
                $this->_objectManager->create('Kahanit\AweMenu\Model\AweMenu'),
                $storeId,
                $params['id']
            ))
        ];
    }

    protected function deleteRevision($params = [])
    {
        $storeId = $this->getRequest()->getParam('store', false);
        if ($storeId === false) {
            return [
                'content-type' => 'json',
                'json' => 'Store is required'
            ];
        }

        return [
            'content-type' => 'json',
            'json' => $this->jsonEncoder->encode($this->aweMenuModel->deleteRevision(
                $this->_objectManager->create('Kahanit\AweMenu\Model\AweMenu'),
                $storeId,
                $params['id']
            ))
        ];
    }

    /* entity functions */

    protected function getEntity($params = [])
    {
        switch ($params['entity']) {
            case 'product':
                return $this->getProducts();
            case 'category':
                return $this->getCategories();
            case 'cmspage':
                return $this->getCMSPages();
        }

        return false;
    }

    protected function getProducts()
    {
        /* init */
        $store = $this->getStore();
        $collection = $this->productFactory->create()->getCollection()
            ->addFieldToSelect('name')
            ->addStoreFilter($store->getId());

        /* search */
        $search = $this->getRequest()->getParam('search', '');
        $search_filtered = $this->aweMenuHelper->filterSearchQuery($search, ['id', 'name']);
        if ($search_filtered['id'] != '') {
            $collection->addFieldToFilter('entity_id', ['=' => $search_filtered['id']]);
        }
        if ($search_filtered['name'] != '') {
            $collection->addFieldToFilter('name', ['like' => '%' . $search_filtered['name'] . '%']);
        }
        if ($search_filtered['id'] == '' && $search_filtered['name'] == '' && $search_filtered['query'] != '') {
            $collection->addFieldToFilter('name', ['like' => '%' . $search_filtered['query'] . '%']);
        }

        /* order */
        $order = $this->getRequest()->getParam('order', [['column' => 0, 'dir' => 'ASC']]);
        $columns = $this->getRequest()->getParam('columns', []);
        if (isset($columns[$order[0]['column']]['data'])) {
            $orderfld = strtolower($columns[$order[0]['column']]['data']);
            if ($orderfld === 'text') {
                $orderfld = 'name';
            } else {
                $orderfld = 'entity_id';
            }
        } else {
            $orderfld = '';
        }
        if (isset($order[0]['dir'])) {
            $orderdir = strtoupper($order[0]['dir']);
        } else {
            $orderdir = '';
        }
        if ($orderfld !== '' && $orderdir !== '') {
            $collection->addOrder($orderfld, $orderdir);
        }

        /* page */
        $start = (int)$this->getRequest()->getParam('start', 0);
        $length = (int)$this->getRequest()->getParam('length', 25);
        $page = $start / $length + 1;
        $collection->setPageSize($length);
        $collection->setCurPage($page);

        /* prepage */
        $products = [];
        $c = 0;
        foreach ($collection->getItems() as $item) {
            $products[$c] = [
                'entity' => 'product',
                'entity_item_id' => $item->getData('entity_id'),
                'text' => ['lang_id_0' => $item->getData('name')]
            ];

            $c++;
        }

        /* return */
        return [
            'content-type' => 'json',
            'json' => $this->jsonEncoder->encode([
                'recordsTotal' => $collection->getSize(),
                'recordsFiltered' => $collection->getSize(),
                'data' => $products
            ])
        ];
    }

    protected function getCategories()
    {
        /* init */
        $store = $this->getStore();
        $collection = $this->categoryFactory->create()->getCollection()
            ->addFieldToSelect('name');

        /* search */
        $search = $this->getRequest()->getParam('search', '');
        $search_filtered = $this->aweMenuHelper->filterSearchQuery($search, ['id', 'name']);
        if ($search_filtered['id'] != '') {
            $collection->addFieldToFilter('entity_id', ['=' => $search_filtered['id']]);
        }
        if ($search_filtered['name'] != '') {
            $collection->addFieldToFilter('name', ['like' => '%' . $search_filtered['name'] . '%']);
        }
        if ($search_filtered['id'] == '' && $search_filtered['name'] == '' && $search_filtered['query'] != '') {
            $collection->addFieldToFilter('name', ['like' => '%' . $search_filtered['query'] . '%']);
        }

        /* parent */
        if ($search === '') {
            $parent_id = $store->getRootCategoryId();
            if ($store->getId() == Store::DEFAULT_STORE_ID) {
                $defautlStoreItems = $this->categoryFactory->create()->getCollection()
                    ->addFieldToFilter('parent_id', ['in' => [$parent_id]]);
                $parent_id = $defautlStoreItems->getFirstItem()->getId();
            }
            $parent_id = (int)$this->getRequest()->getParam('parent_id', $parent_id);
            $collection->addFieldToFilter('parent_id', ['in' => [$parent_id]]);
        }

        /* prepare */
        $categories = [];
        $c = 0;
        foreach ($collection->getItems() as $item) {
            $categories[$c] = [
                'entity' => 'category',
                'entity_item_id' => $item->getData('entity_id'),
                'key' => $item->getData('entity_id'),
                'folder' => true,
                'expanded' => false,
                'lazy' => true,
                'text' => ['lang_id_0' => $item->getData('name')],
                'title' => $item->getData('name')
            ];
            if ($search !== '') {
                $categories[$c]['expanded'] = true;
                $categories[$c]['lazy'] = false;
            }

            $c++;
        }

        /* return */
        return [
            'content-type' => 'json',
            'json' => $this->jsonEncoder->encode($categories)
        ];
    }

    protected function getCMSPages()
    {
        /* init */
        $store = $this->getStore();
        $collection = $this->pageFactory->create()->getCollection()
            ->addFieldToSelect('page_id')
            ->addFieldToSelect('title')
            ->addFieldToFilter('store_id', ['in' => [$store->getId()]]);

        /* search */
        $search = $this->getRequest()->getParam('search', '');
        $search_filtered = $this->aweMenuHelper->filterSearchQuery($search, ['id', 'title']);
        if ($search_filtered['id'] != '') {
            $collection->addFieldToFilter('page_id', ['=' => $search_filtered['id']]);
        }
        if ($search_filtered['title'] != '') {
            $collection->addFieldToFilter('title', ['like' => '%' . $search_filtered['title'] . '%']);
        }
        if ($search_filtered['id'] == '' && $search_filtered['title'] == '' && $search_filtered['query'] != '') {
            $collection->addFieldToFilter('title', ['like' => '%' . $search_filtered['query'] . '%']);
        }

        /* order */
        $order = $this->getRequest()->getParam('order', [['column' => 0, 'dir' => 'ASC']]);
        $columns = $this->getRequest()->getParam('columns', []);
        if (isset($columns[$order[0]['column']]['data'])) {
            $orderfld = strtolower($columns[$order[0]['column']]['data']);
            if ($orderfld === 'text') {
                $orderfld = 'title';
            } else {
                $orderfld = 'page_id';
            }
        } else {
            $orderfld = '';
        }
        if (isset($order[0]['dir'])) {
            $orderdir = strtoupper($order[0]['dir']);
        } else {
            $orderdir = '';
        }
        if ($orderfld !== '' && $orderdir !== '') {
            $collection->addOrder($orderfld, $orderdir);
        }

        /* page */
        $start = (int)$this->getRequest()->getParam('start', 0);
        $length = (int)$this->getRequest()->getParam('length', 25);
        $page = $start / $length + 1;
        $collection->setPageSize($length);
        $collection->setCurPage($page);

        /* prepage */
        $cmspages = [];
        $c = 0;
        foreach ($collection->getItems() as $item) {
            $cmspages[$c] = [
                'entity' => 'cmspage',
                'entity_item_id' => $item->getData('page_id'),
                'text' => ['lang_id_0' => $item->getData('title')]
            ];

            $c++;
        }

        /* return */
        return [
            'content-type' => 'json',
            'json' => $this->jsonEncoder->encode([
                'recordsTotal' => $collection->getSize(),
                'recordsFiltered' => $collection->getSize(),
                'data' => $cmspages
            ])
        ];
    }
}
