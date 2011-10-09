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
        /** @var \Doctrine\ORM\EntityManager $em  */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $project = new Project();
        $project->setName($input->getArgument('name'));
        $project->setUrl($input->getArgument('url'));

        /** @var \Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface $sourceCodeRepo  */
        $sourceCodeRepo = $this->getContainer()->get('opensoft_codeconversation.source_code.repository');
        $sourceCodeRepo->init($project, function ($type, $buffer) use ($output) {
            if ('err' === $type) {
                $output->write(str_replace("\n", "\nERR| ", $buffer));
            } else {
                $output->write(str_replace("\n", "\nOUT| ", $buffer));
            }
        });

        $em->persist($project);
        $em->flush();

        $this->synchronizeBranches($em, $project, $sourceCodeRepo);

        $output->writeln(strtr("Project <info>%project%</info> created!", array('%project%' => $project->getName())));
    }
}
