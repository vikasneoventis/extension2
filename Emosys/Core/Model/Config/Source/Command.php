<?php
/**
 * @author Emosys Team
 * @copyright Copyright (c) 2016 Emosys Ltd (http://www.emosys.com)
 * @package Emosys_Core
 */
namespace Emosys\Core\Model\Config\Source;

class Command implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'help', 'label' => __('Help')],
            ['value' => 'list', 'label' => __('List')],

            [
                'value' => [
                    ['value' => 'setup:backup', 'label' => __('Backup')],
                    ['value' => 'setup:config:set', 'label' => __('Config Set')],
                    ['value' => 'setup:cron:run', 'label' => __('Cron Run')],
                    ['value' => 'setup:db-data:upgrade', 'label' => __('DB Data Upgrade')],
                    ['value' => 'setup:db-schema:upgrade', 'label' => __('DB Schema Upgrade')],
                    ['value' => 'setup:db:status', 'label' => __('DB Status')],
                    ['value' => 'setup:di:compile', 'label' => __('DI Compile')],
                    ['value' => 'setup:di:compile-multi-tenant', 'label' => __('DI Compile Multi Tenant')],
                    ['value' => 'setup:install', 'label' => __('Install')],
                    ['value' => 'setup:performance:generate-fixtures', 'label' => __('Performance Generate Fixtures')],
                    ['value' => 'setup:rollback', 'label' => __('Rollback')],
                    ['value' => 'setup:static-content:deploy', 'label' => __('Static Content Deploy')],
                    ['value' => 'setup:store-config:set', 'label' => __('Store Config Set')],
                    ['value' => 'setup:uninstall', 'label' => __('Uninstall')],
                    ['value' => 'setup:upgrade', 'label' => __('Upgrade')]
                ],
                'label' => __('Setup')
            ],

            [
                'value' => [
                    ['value' => 'module:disable', 'label' => __('Disable')],
                    ['value' => 'module:enable', 'label' => __('Enable')],
                    ['value' => 'module:status', 'label' => __('Status')],
                    ['value' => 'module:uninstall', 'label' => __('Uninstall')]
                ],
                'label' => __('Module')
            ],

            [
                'value' => [
                    ['value' => 'indexer:info', 'label' => __('Info')],
                    ['value' => 'indexer:reindex', 'label' => __('Reindex')],
                    ['value' => 'indexer:set-mode', 'label' => __('Set Mode')],
                    ['value' => 'indexer:show-mode', 'label' => __('Show Mode')],
                    ['value' => 'indexer:status', 'label' => __('Status')]
                ],
                'label' => __('Indexer')
            ],

            [
                'value' => [
                    ['value' => 'cache:clean', 'label' => __('Clean')],
                    ['value' => 'cache:disable', 'label' => __('Disable')],
                    ['value' => 'cache:enable', 'label' => __('Enable')],
                    ['value' => 'cache:flush', 'label' => __('Flush')],
                    ['value' => 'cache:status', 'label' => __('Status')]
                ],
                'label' => __('Cache')
            ],

            [
                'value' => [
                    ['value' => 'admin:user:create', 'label' => __('User Create')],
                    ['value' => 'admin:user:unlock', 'label' => __('User Unlock')]
                ],
                'label' => __('Admin')
            ],

            [
                'value' => [
                    ['value' => 'catalog:images:resize', 'label' => __('Image Resize')]
                ],
                'label' => __('Catalog')
            ],

            [
                'value' => [
                    ['value' => 'cron:run', 'label' => __('Run')]
                ],
                'label' => __('Cron')
            ],

            [
                'value' => [
                    ['value' => 'customer:hash:upgrade', 'label' => __('Hash Upgrade')]
                ],
                'label' => __('Customer')
            ],

            [
                'value' => [
                    ['value' => 'deploy:mode:set', 'label' => __('Mode Set')],
                    ['value' => 'deploy:mode:show', 'label' => __('Mode Show')]
                ],
                'label' => __('Deploy')
            ],

            [
                'value' => [
                    ['value' => 'dev:source-theme:deploy', 'label' => __('Source Theme Deploy')],
                    ['value' => 'dev:tests:run', 'label' => __('Tests Run')],
                    ['value' => 'dev:urn-catalog:generate', 'label' => __('URN Catalog Generate')],
                    ['value' => 'dev:xml:convert', 'label' => __('XML Convert')]
                ],
                'label' => __('Dev')
            ],

            [
                'value' => [
                    ['value' => 'i18n:collect-phrases', 'label' => __('Collect Phrases')],
                    ['value' => 'i18n:pack', 'label' => __('Pack')],
                    ['value' => 'i18n:uninstall', 'label' => __('Uninstall')]
                ],
                'label' => __('I18N')
            ],

            [
                'value' => [
                    ['value' => 'info:adminuri', 'label' => __('Admin URI')],
                    ['value' => 'info:backups:list', 'label' => __('Backups List')],
                    ['value' => 'info:currency:list', 'label' => __('Currency List')],
                    ['value' => 'info:dependencies:show-framework', 'label' => __('Dependencies Show Framework')],
                    ['value' => 'info:dependencies:show-modules', 'label' => __('Dependencies Show Modules')],
                    ['value' => 'info:dependencies:show-modules-circular', 'label' => __('Dependencies Show Modules Circular')],
                    ['value' => 'info:language:list', 'label' => __('Language List')],
                    ['value' => 'info:timezone:list', 'label' => __('Timezone List')]
                ],
                'label' => __('Info')
            ],

            [
                'value' => [
                    ['value' => 'maintenance:allow-ips', 'label' => __('Allow IPs')],
                    ['value' => 'maintenance:disable', 'label' => __('Disable')],
                    ['value' => 'maintenance:enable', 'label' => __('Enable')],
                    ['value' => 'maintenance:status', 'label' => __('Status')]
                ],
                'label' => __('Maintenance')
            ],

            [
                'value' => [
                    ['value' => 'sampledata:deploy', 'label' => __('Deploy')],
                    ['value' => 'sampledata:remove', 'label' => __('Remove')],
                    ['value' => 'sampledata:reset', 'label' => __('Reset')]
                ],
                'label' => __('Sample Data')
            ],

            [
                'value' => [
                    ['value' => 'theme:uninstall', 'label' => __('Uninstall')]
                ],
                'label' => __('Theme')
            ]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'help' => __('Help'),
            'list' => __('List'),

            'setup:backup' => __('Setup: Backup'),
            'setup:config:set' => __('Setup: Config Set'),
            'setup:cron:run' => __('Setup: Cron Run'),
            'setup:db-data:upgrade' => __('Setup: DB Data Upgrade'),
            'setup:db-schema:upgrade' => __('Setup: DB Schema Upgrade'),
            'setup:db:status' => __('Setup: DB Status'),
            'setup:di:compile' => __('Setup: DI Compile'),
            'setup:di:compile-multi-tenant' => __('Setup: DI Compile Multi Tenant'),
            'setup:install' => __('Setup: Install'),
            'setup:performance:generate-fixtures' => __('Setup: Performance Generate Fixtures'),
            'setup:rollback' => __('Setup: Rollback'),
            'setup:static-content:deploy' => __('Setup: Static Content Deploy'),
            'setup:store-config:set' => __('Setup: Store Config Set'),
            'setup:uninstall' => __('Setup: Uninstall'),
            'setup:upgrade' => __('Setup: Upgrade'),

            'module:disable' => __('Module: Disable'),
            'module:enable' => __('Module: Enable'),
            'module:status' => __('Module: Status'),
            'module:uninstall' => __('Module: Uninstall'),

            'indexer:info' => __('Indexer: Info'),
            'indexer:reindex' => __('Indexer: Reindex'),
            'indexer:set-mode' => __('Indexer: Set Mode'),
            'indexer:show-mode' => __('Indexer: Show Mode'),
            'indexer:status' => __('Indexer: Status'),

            'cache:clean' => __('Cache: Clean'),
            'cache:disable' => __('Cache: Disable'),
            'cache:enable' => __('Cache: Enable'),
            'cache:flush' => __('Cache: Flush'),
            'cache:status' => __('Cache: Status'),

            'admin:user:create' => __('Admin: User Create'),
            'admin:user:unlock' => __('Admin: User Unlock'),

            'catalog:images:resize' => __('Catalog: Image Resize'),

            'cron:run' => __('Cron: Run'),

            'customer:hash:upgrade' => __('Customer: Hash Upgrade'),

            'deploy:mode:set' => __('Deploy: Mode Set'),
            'deploy:mode:show' => __('Deploy: Mode Show'),

            'dev:source-theme:deploy' => __('Dev: Source Theme Deploy'),
            'dev:tests:run' => __('Dev: Tests Run'),
            'dev:urn-catalog:generate' => __('Dev: URN Catalog Generate'),
            'dev:xml:convert' => __('Dev: XML Convert'),

            'i18n:collect-phrases' => __('I18N: Collect Phrases'),
            'i18n:pack' => __('I18N: Pack'),
            'i18n:uninstall' => __('I18N: Uninstall'),

            'info:adminuri' => __('Info: Admin URI'),
            'info:backups:list' => __('Info: Backups List'),
            'info:currency:list' => __('Info: Currency List'),
            'info:dependencies:show-framework' => __('Info: Dependencies Show Framework'),
            'info:dependencies:show-modules' => __('Info: Dependencies Show Modules'),
            'info:dependencies:show-modules-circular' => __('Info: Dependencies Show Modules Circular'),
            'info:language:list' => __('Info: Language List'),
            'info:timezone:list' => __('Info: Timezone List'),

            'maintenance:allow-ips' => __('Maintenance: Allow IPs'),
            'maintenance:disable' => __('Maintenance: Disable'),
            'maintenance:enable' => __('Maintenance: Enable'),
            'maintenance:status' => __('Maintenance: Status'),

            'sampledata:deploy' => __('Sample Data: Deploy'),
            'sampledata:remove' => __('Sample Data: Remove'),
            'sampledata:reset' => __('Sample Data: Reset'),

            'theme:uninstall' => __('Theme: Uninstall')
        ];
    }
}
