<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Emosys\Core\Controller\Adminhtml\System\Config\Clear;

use Magento\Framework\Controller\Result\JsonFactory;

class Js extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Filesystem facade
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_filesystem = $filesystem;
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = array();
        /*
        $directory = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $root = $directory->getAbsolutePath();

        $command = "find {$root}pub/static/frontend/Emosys/. -type f -name '*.js' -delete";
        $result[] = $this->_execute($command);

        $command = "php {$root}bin/magento setup:static-content:deploy";
        $result[] = $this->_execute($command);
        */
        $command = "find pub/static/frontend/Emosys/. -type f -name '*.js' -delete";
        $result[] = $this->_execute($command);

        $command = "php bin/magento setup:static-content:deploy";
        $result[] = $this->_execute($command);

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData([
            'valid' => 1,
            'message' => implode('', $result),
        ]);
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function _execute($command)
    {
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

        return $result;
    }
}
