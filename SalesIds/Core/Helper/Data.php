<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\Core\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config paths
     */
    const XML_PATH_NOTIFICATION_ENABLED = 'salesids_core_extensions/admin_notification/enabled';

    /**
     * Check if notifications are enabled
     *
     * @return bool
     */
    public function isNotificationEnabled()
    {
        if (!$this->isModuleOutputEnabled('Magento_AdminNotification')) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NOTIFICATION_ENABLED
        );
    }
}
