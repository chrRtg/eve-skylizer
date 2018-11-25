<?php

namespace Application\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use VposMoon\Controller\MoonController;


class AllyCorpUpdateCommand extends Command
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
            ->setName('update:allycorp')
            ->setDescription('Update alliances and corporations from ESI')
            ->addOption(
                'ally',
                null,
                InputOption::VALUE_NONE,
                'If set, only alliances get updated'
            )
            ->addOption(
                'corp',
                null,
                InputOption::VALUE_NONE,
                'If set, only corporations get updated'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'standard mode is to fetch only new entries, with \'all\' anything is fetches (may take hours)'
            );
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Running skylizer update application");
        $output->writeln("please wait, may take a while ...");

        $forcemode = false;
        $res = '';

        if ($input->getOption('all')) {
            $forcemode = true;
        }
    
        if ($input->getOption('ally') && $input->getOption('corp')) {
            $res = $this->moonController->allyCorpUpdateConsole($forcemode, 'b');
        } else if ($input->getOption('ally')) {
            $res = $this->moonController->allyCorpUpdateConsole($forcemode, 'a');
        } else if ($input->getOption('corp')) {
            $res = $this->moonController->allyCorpUpdateConsole($forcemode, 'c');
        } else {
            $res = 'PROBLEM: you should choose at least one option of ally or corp in order to update anything';
        }

        $output->writeln($res);
    }
}