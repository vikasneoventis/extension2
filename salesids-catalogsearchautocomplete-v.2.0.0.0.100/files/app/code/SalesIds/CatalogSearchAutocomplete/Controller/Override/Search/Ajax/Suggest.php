<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\CatalogSearchAutocomplete\Controller\Override\Search\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Search\Model\QueryFactory;
use SalesIds\CatalogSearchAutocomplete\Helper\Data as DataHelper;

class Suggest extends \Magento\Search\Controller\Ajax\Suggest
{
    /**
     * Maximum results to display
     *
     * @var int
     */
    const MAX_RESULT_DISPLAY = 5;

    /**
     * Autocomplete
     *
     * @var  AutocompleteInterface
     */
    private $autocomplete;

    /**
     * Query factory
     *
     * @var QueryFactory
     */
    protected $_queryFactory;

    /**
     * Search helper
     *
     * @var SearchHelper
     */
    protected $_searchHelper;

    /**
     * Data helper
     *
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * Format response data
     *
     * @param Collection $collection
     * @param Query $query
     * @return array
     */
    protected function _formatData($collection, $query)
    {
        return [
            'results' => $this->_limitResponseData($collection),
            'info' => [
                'size' => count($collection),
                'url'  => $this->_searchHelper->getResultUrl($query->getQueryText())
            ],
        ];
    }

    /**
     * Limit response elements
     *
     * @param array $responseData Response Data
     * @return array
     */
    protected function _limitResponseData($responseData)
    {
        return array_slice($responseData, 0, self::MAX_RESULT_DISPLAY);
    }

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param AutocompleteInterface $autocomplete
     * @param QueryFactory $queryFactory
     * @param DataHelper $dataHelper
     * @param SearchHelper $searchHelper
     * @return void
     */
    public function __construct(
        Context $context,
        AutocompleteInterface $autocomplete,
        QueryFactory $queryFactory,
        DataHelper $dataHelper,
        SearchHelper $searchHelper
    ) {
        $this->autocomplete = $autocomplete;
        $this->_queryFactory = $queryFactory;
        $this->_searchHelper = $searchHelper;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $autocomplete);
    }

    /**
     * Render results
     *
     * @return Json
     */
    public function execute()
    {
        if (!$this->_dataHelper->isEnabled()) {
            return parent::execute();
        }

        $this->_view->loadLayout();

        if (!$this->getRequest()->getParam('q', false)) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }

        /* @var $query \Magento\Search\Model\Query */
        $query = $this->_queryFactory->get();

        $autocompleteItems = $this->autocomplete->getItems();
        if ($query->getQueryText() != '') {
            if ($this->_objectManager->get('Magento\CatalogSearch\Helper\Data')->isMinQueryLength()) {
                $query->setId(0)->setIsActive(1)->setIsProcessed(1);
            } else {
                $query->saveIncrementalPopularity();

                // Save query into database
                $query->saveNumResults(sizeof($autocompleteItems));
            }
        }

        $responseData = $this->_formatData($autocompleteItems, $query);

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;
    }
}
