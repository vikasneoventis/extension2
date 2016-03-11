<?php
///**
// * Created by: IWD Agency "iwdagency.com"
// * Developer: Andrew Chornij "iwd.andrew@gmail.com"
// * Date: 11.12.2015
// * Copyright © 2015 Magento. All rights reserved.
// * See COPYING.txt for license details.
// */
//namespace IWD\NewsletterPopup\Setup;
//
//use Magento\Framework\Setup;
//
//class Installer implements Setup\SampleData\InstallerInterface
//{
//    /**
//     * @var \IWD\NewsletterPopup\Model\Block
//     */
//    private $block;
//
//    /**
//     * @param \Magento\CmsSampleData\Model\Block $block
//     */
//    public function __construct(
//        \Magento\CmsSampleData\Model\Block $block
//    ) {
//        $this->block = $block;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function install()
//    {
//        $this->block->install([ 'Magento_NewsletterPopup::setup/fixtures/blocks/categories_static_blocks.csv', ]);
//    }
//}