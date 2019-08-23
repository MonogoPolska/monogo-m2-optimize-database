<?php

namespace Monogo\OptimizeDatabase\Console\Command;

use Monogo\OptimizeDatabase\Helper\Data;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * PHP version 7.0
 * Class Run
 *
 * @category Monogo
 * @package  Monogo\OptimizeDatabase
 * @license  MIT
 * @author   Paweł Detka <pawel.detka@monogo.pl>
 */
class Run extends Command
{
    const MODE = 'mode';

    protected $helper;
    /**
     * FrontendPing constructor.
     *
     * @param \Magento\Framework\App\State      $appState State
     * @param \Monogo\OptimizeDatabase\Helper\Data $helper   Helper
     *
     * @throws \Exception
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct();
    }

    /**
     * Command Configuration
     *
     * @return void
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::MODE,
                null,
                InputOption::VALUE_REQUIRED,
                'Mode'
            ),
        ];

        $this->setName('monogo:optimize:database')
            ->setDescription('Optimize database')
            ->setDefinition($options);
        parent::configure();
    }

    /**
     * Execute
     *
     * @param InputInterface  $input  InputInterface
     * @param OutputInterface $output OutputInterface
     *
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        } catch (\Exception $e) {
        }
        if ($mode = $input->getOption(self::MODE)) {
            switch ($mode) {
            case 'print':
                $this->helper->printTables();
                break;
            case 'optimize':
                $this->helper->optimizeTables(true);
                break;
            default:
                echo $this->usageHelp();
                break;
            }
        } else {
            echo $this->usageHelp();
        }
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php bin/magento monogo:optimize:database [--mode MODE]

  print                             Print all tables
  optimize                          Optimize tables       
  help                              This help

USAGE;
    }
}
