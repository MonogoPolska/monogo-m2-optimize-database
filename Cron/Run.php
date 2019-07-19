<?php

namespace Monogo\OptimizeDatabase\Cron;

use Monogo\OptimizeDatabase\Helper\Data;

/**
 * PHP version 7.0
 * Class Run
 *
 * @category Monogo
 * @package  Monogo\OptimizeDatabase
 * @license  MIT
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Run
{
    protected $helper;

    /**
     * RemindEmail constructor.
     *
     * @param Data $helper Data
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Optimize tables
     *
     * @return void
     */
    public function optimizeTables()
    {
        if ($this->helper->isEnabled() && $this->helper->useCron()) {
            $this->helper->optimizeTables(false);
        }
    }

    /**
     * Resend Emails
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $this->optimizeTables();
    }
}
