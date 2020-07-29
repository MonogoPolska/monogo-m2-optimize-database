<?php

declare(strict_types=1);

namespace Monogo\OptimizeDatabase\Cron;

use Monogo\OptimizeDatabase\Helper\Data;

/**
 * Class Run
 *
 * @category Monogo
 * @package  Monogo\OptimizeDatabase
 * @license  MIT
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Run
{
    /**
     * @var Data
     */
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
    public function optimizeTables() : void
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
    public function execute() : void
    {
        $this->optimizeTables();
    }
}
