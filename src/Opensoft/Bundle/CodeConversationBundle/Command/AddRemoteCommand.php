<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Opensoft\Bundle\CodeConversationBundle\Entity\Project;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class AddRemoteCommand extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('opensoft:code:add-remote');
        $this->addArgument('project', InputArgument::REQUIRED, 'The project\'s name');
        $this->addArgument('remote', InputArgument::REQUIRED, 'The remote\'s name');
        $this->addArgument('url', InputArgument::REQUIRED, 'The project\'s url, as pulled by git');
        $this->addOption('username', null, InputOption::VALUE_OPTIONAL, '[Optional] The username to connect to the remote');
        $this->addOption('password', null, InputOption::VALUE_OPTIONAL, '[Optional] The password used to connect to the remote');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Opensoft\Bundle\CodeConversationBundle\Model\ProjectManagerInterface $projectManager  */
        $projectManager = $this->getContainer()->get('opensoft_codeconversation.manager.project');

        $project = $projectManager->findProjectBy(array('name' => $input->getArgument('project')));

        foreach ($project->getRemotes() as $remote) {
            if ($remote->getName() == $input->getArgument('remote')) {
                throw new \RuntimeException(sprintf("Remote %s already exists for project %s", $input->getArgument('remote'), $project->getName()));
            }
        }

        $projectManager->createRemote()

        
        $project->initSourceCodeRepo(function ($type, $buffer) use ($output) {
            if ('err' === $type) {
                $output->write(str_replace("\n", "\nERR| ", $buffer));
            } else {
                $output->write(str_replace("\n", "\nOUT| ", $buffer));
            }
        });

        $project->synchronizeBranches();
        
        $projectManager->updateProject($project);

        $output->writeln(strtr("Project <info>%project%</info> created!", array('%project%' => $project->getName())));
    }
}
