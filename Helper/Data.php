<?php

namespace Monogo\OptimizeDatabase\Helper;

use LucidFrame\Console\ConsoleTable;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * PHP version 7.0
 * Class Data
 *
 * @category Monogo
 * @package  Monogo\OptimizeDatabase
 * @license  MIT
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Data extends AbstractHelper
{
    const CONFIG_PATH_ENABLED = 'monogo_optimizedatabase/general/enabled';

    const CONFIG_PATH_USE_CRON = 'monogo_optimizedatabase/general/use_cron';

    const CONFIG_PATH_MIN_FRAG_RATIO = 'monogo_optimizedatabase/general/min_frag_ratio';

    protected $storeManager;

    protected $context;

    protected $resourceConnection;

    protected $consoleTable;

    protected $deploymentConfig;

    /**
     * Data constructor.
     *
     * @param Context               $context            Context
     * @param StoreManagerInterface $storeManager       StoreManagerInterface
     * @param ResourceConnection    $resourceConnection ResourceConnection
     * @param ConsoleTable          $consoleTable       ConsoleTable
     * @param DeploymentConfig      $deploymentConfig   DeploymentConfig
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        ConsoleTable $consoleTable,
        DeploymentConfig $deploymentConfig
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->consoleTable = $consoleTable;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * Get Store Config by key
     *
     * @param string $config_path Path
     * @param int    $storeId     StoreId
     *
     * @return mixed
     */
    public function getConfig($config_path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Is Enabled
     *
     * @return int
     */
    public function isEnabled()
    {
        return $this->getConfig(self::CONFIG_PATH_ENABLED);
    }

    /**
     * Get Use Cron
     *
     * @return int
     */
    public function useCron()
    {
        return $this->getConfig(self::CONFIG_PATH_USE_CRON);
    }

    /**
     * Get Min fragmentation ration
     *
     * @return int
     */
    public function getMinFragRation()
    {
        $min_frag_ratio = $this->getConfig(self::CONFIG_PATH_MIN_FRAG_RATIO);
        if (empty($min_frag_ratio)) {
            $min_frag_ratio = 1;
        }
        return $min_frag_ratio;
    }

    /**
     * Get Tables
     *
     * @return array
     */
    public function getTables()
    {
        $readConnection = $this->resourceConnection->getConnection();
        $db_name = $this->deploymentConfig
            ->get(
                ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
                . '/' . ConfigOptionsListConstants::KEY_NAME
            );

        $query = 'select  ENGINE, TABLE_SCHEMA,TABLE_NAME,Round( DATA_LENGTH/1024/1024) as data_length , round(INDEX_LENGTH/1024/1024) as index_length, round(DATA_FREE/ 1024/1024) as data_free, (data_free/(index_length+data_length)) as frag_ratio from information_schema.tables  where TABLE_SCHEMA ="' . $db_name . '" AND DATA_FREE > 0  order by frag_ratio desc;';
        return $readConnection->fetchAll($query);
    }

    /**
     * Print tables
     *
     * @return void
     */
    public function printTables()
    {
        $tbl = $this->consoleTable;
        $tbl->setHeaders(
            ['Table name', 'Data length', 'Index length', 'Data free', 'Frag Ratio']
        );
        foreach ($this->getTables() as $result) {
            $tbl->addRow(
                [
                    $result['TABLE_NAME'],
                    $result['data_length'],
                    $result['index_length'],
                    $result['data_free'],
                    $result['frag_ratio'],
                ]
            );
        }
        $tbl->display();
    }

    /**
     * Optimize tables
     *
     * @param bool $console Is Console mode
     *
     * @return void
     */
    public function optimizeTables($console = false)
    {
        if (!$this->isEnabled()) {
            if ($console) {
                print_r('Module is disabled in Stores->Configuration->Monogo->Optimize database' . PHP_EOL);
            }
            return null;
        }

        $writeConnection = $this->resourceConnection->getConnection();
        $min_frag_ratio = $this->getMinFragRation();
        foreach ($this->getTables() as $table) {
            if ($table['frag_ratio'] < $min_frag_ratio) {
                return;
            }
            try {
                if ($console) {
                    print_r($table['TABLE_NAME'] . PHP_EOL);
                }
                $query = "OPTIMIZE TABLE " . $table['TABLE_NAME'];
                $writeConnection->query($query);
            } catch (\Exception $e) {
                print_r($e->getMessage());
            }
        }
    }
}
