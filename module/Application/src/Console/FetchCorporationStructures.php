<?php

/**
 * Fetch Corporation data for all structures
 */

namespace Application\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use VposMoon\Controller\VposController;


class FetchCorporationStructures extends Command
{
    /**
     *
     * @var Zend\Servi
     */
    private $vposController;
    
    /**
     * Constructor
     */
    public function __construct($serviceManager)
    {
        parent::__construct();
        $this->serviceManager = $serviceManager;
        $this->vposController = $serviceManager->get('ControllerManager')->get(VposController::class);
    }

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName('update:structures')
            ->setDescription('Fetch all corporation structures and their mining activity');
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Running skylizer Corporation Structures updater");
        $output->writeln("please wait, may take a while ...");

        $cnt = $this->vposController->fetchCoprporationStructuresConsole();
        $output->writeln($cnt . " structures found");
    }
}