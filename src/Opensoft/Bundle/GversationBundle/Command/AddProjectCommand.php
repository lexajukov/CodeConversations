<?php
/*
 *
 */

namespace Opensoft\Bundle\GversationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Opensoft\Bundle\GversationBundle\Entity\Project;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class AddProjectCommand extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('opensoft:gversation:add-project');
        $this->addArgument('name', InputArgument::REQUIRED, 'The project\'s name');
        $this->addArgument('url', InputArgument::REQUIRED, 'The project\'s url, as pulled by git');
        $this->addOption('username', null, InputOption::VALUE_OPTIONAL, '[Optional] The username to connect to the project');
        $this->addOption('password', null, InputOption::VALUE_OPTIONAL, '[Optional] The password used to connect to the project');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Doctrine\ORM\EntityManager $em  */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $project = new Project();
        $project->setName($input->getArgument('name'));
        $project->setUrl($input->getArgument('url'));

        $em->persist($project);
        $em->flush();

        /** @var \Opensoft\Bundle\GversationBundle\Git\Builder $builder  */
        $builder = $this->getContainer()->get('opensoft_gversation.git.builder');
        $builder->init($project);
        

        $output->writeln("Project created");
    }
}
