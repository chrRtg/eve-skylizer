<?php

namespace Application\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use VposMoon\Controller\MoonController;


class ExportMoongooCommand extends Command
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
            ->setName('export:moongoo')
            ->setDescription('export MoonGoo data')
            ->addOption(
                'user',
                'u',
                InputOption::VALUE_REQUIRED,
                'export all data from a given user by his ID'
            )
            ->addOption(
                'system',
                's',
                InputOption::VALUE_REQUIRED,
                'export all data for a solar system by his ID'
            )
            ->addOption(
                'constellation',
                'c',
                InputOption::VALUE_REQUIRED,
                'export all data for a constellation by her ID'
            );
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Running skylizer moongoo export application");
        $output->writeln("please wait, may take a while ...");

        $res = '';

        if ($input->getOption('user')) {
            $output->writeln("Export MoonGoo scanned by userID  :: " . $input->getOption('user'));
            $res = $this->moonController->moonGooExportConsole($input->getOption('user'), 'u');
        } elseif ($input->getOption('system')) {
            $output->writeln("Export MoonGoo scanned by systemID  :: " . $input->getOption('system'));
            $res = $this->moonController->moonGooExportConsole($input->getOption('system'), 's');
        } elseif ($input->getOption('constellation')) {
            $output->writeln("Export MoonGoo scanned by constellationID  :: " . $input->getOption('constellation'));
            $res = $this->moonController->moonGooExportConsole($input->getOption('constellation'), 'c');
        } else {
            $res = 'PROBLEM: you should choose at least one option';
        }

        $output->writeln($res);
    }
}