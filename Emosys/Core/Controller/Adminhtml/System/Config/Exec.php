<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Emosys\Core\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;

class Exec extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $command = $this->getRequest()->getParam('command');
        $args = $this->getRequest()->getParam('args');

        /* Check argument */
        if ($args) {
            $command = "php bin/magento {$command} {$args}";
        } else {
            $command = "php bin/magento {$command}";
        }

        /* Alias expansion. */
        $length = strcspn($command, " \t");
        $token = substr($command, 0, $length);

        $io = array();
        $pointer = proc_open(
            $command,
            array(
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w')
            ),
            $io,
            BP
        );

        $result = null;

        /* Read output sent to stdout. */
        while ( !feof($io[1]) ) {
            $line = fgets($io[1]);
            if ( function_exists('mb_convert_encoding') ) {
                /* (hopefully) fixes a strange "htmlspecialchars(): Invalid multibyte sequence in argument" error */
                $line = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
            }
            #$result[] = htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
            $result .= $line;
        }

        /* Read output sent to stderr. */
        while ( !feof($io[2]) ) {
            $line = fgets($io[2]);
            if ( function_exists('mb_convert_encoding') ) {
                /* (hopefully) fixes a strange "htmlspecialchars(): Invalid multibyte sequence in argument" error */
                $line = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
            }
            #$result[] = htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
            $result .= $line;
        }
        
        fclose($io[1]);
        fclose($io[2]);
        proc_close($pointer);

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setData([
            'valid' => 1,
            'message' => $result,
        ]);
    }
}
