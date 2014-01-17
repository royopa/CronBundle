<?php
/**
 *
 */

namespace SymfonyContrib\Bundle\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CronRunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cron:run')
            ->setDescription('Run cron jobs.')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of a specific cron job to run.')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Run all known cron jobs.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cronExecuter = $this->getContainer()->get('cron.executer');
        $response = '';

        if ($name = $input->getArgument('name')) {
            $cronExecuter->runByName($name);
        } elseif ($input->getOption('all')) {
            $cronExecuter->runAll();
        } else {
            $cronExecuter->runDue();
        }

        $output->writeln($response);
    }
}
