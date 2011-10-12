<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Command;

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
class AddProjectCommand extends BaseCommand
{

    public function configure()
    {
        $this->setName('opensoft:code:add-project');
        $this->addArgument('name', InputArgument::REQUIRED, 'The project\'s name');
        $this->addArgument('url', InputArgument::REQUIRED, 'The project\'s url, as pulled by git');
        $this->addOption('username', null, InputOption::VALUE_OPTIONAL, '[Optional] The username to connect to the project');
        $this->addOption('password', null, InputOption::VALUE_OPTIONAL, '[Optional] The password used to connect to the project');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Opensoft\Bundle\CodeConversationBundle\Model\ProjectManagerInterface $projectManager  */
        $projectManager = $this->getContainer()->get('opensoft_codeconversation.manager.project');

        /** @var \Opensoft\Bundle\CodeConversationBundle\Model\RemoteManagerInterface $remoteManager  */
        $remoteManager = $this->getContainer()->get('opensoft_codeconversation.manager.remote');

        $project = $projectManager->createProject();
        $project->setName($input->getArgument('name'));

        $projectManager->updateProject($project);

        $remote = $remoteManager->createRemote();
        $remote->setName('origin'); // new projects always get a remote named origin...
        $remote->setUrl($input->getArgument('url'));
        $remote->setUsername($input->getOption('username'));
        $remote->setPassword($input->getOption('password'));
        $remote->setProject($project);
        
        $remoteManager->updateRemote($remote);

//        $project->addRemote($remote);
        $project->setDefaultRemote($remote);

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\RepositoryManager $repoManager  */
        $repoManager = $this->getContainer()->get('opensoft_codeconversation.repository_manager');

        $projectManager->updateProject($project);

        $repository = $repoManager->getRepository($project);
        $this->synchronizeBranches($output, $repository, $project);

        foreach ($remote->getBranches() as $branch) {
            if ($branch->getName() == 'master') {
                $remote->setHeadBranch($branch);
                $remoteManager->updateRemote($remote);

                break;
            }
        }


        $output->writeln(strtr("Project <info>%project%</info> created!", array('%project%' => $project->getName())));
    }

//    $repo->init($project, $remote, function ($type, $buffer) use ($output) {
//            if ('err' === $type) {
//                $output->write(str_replace("\n", "\nERR| ", $buffer));
//            } else {
//                $output->write(str_replace("\n", "\nOUT| ", $buffer));
//            }
//        });
}
