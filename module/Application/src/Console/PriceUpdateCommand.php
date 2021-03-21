<?php

namespace Application\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use VposMoon\Controller\MoonController;


class PriceUpdateCommand extends Command
{
    /**
     *
     * @var Zend\Servi
     */
    private $moonController;
    
    /**
     * Constructor
     */
    public function __construct($serviceManager)
    {
        parent::__construct();
        $this->serviceManager = $serviceManager;
        $this->moonController = $serviceManager->get('ControllerManager')->get(MoonController::class);
    }

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName('update:prices-all')
            ->setDescription('Update all prices from ESI');
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Running skylizer price-update application");
        $output->writeln("please wait, may take a while ...");

        $cnt = $this->moonController->priceUpdateConsole();
        $output->writeln($cnt . " prices updated");
    }
}