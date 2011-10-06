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
use Opensoft\Bundle\CodeConversationBundle\Entity\Branch;
use Opensoft\Bundle\CodeConversationBundle\Git\Builder;
use Doctrine\ORM\EntityManager;

/**
 * Synchronizes database with git repositories for projects
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class SynchronizeCommand extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('opensoft:code:sync');
        $this->addArgument('name', InputArgument::OPTIONAL, 'The project\'s name');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Doctrine\ORM\EntityManager $em  */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        if ($projectName = $input->getArgument('name')) {
            $projects = array($em->getRepository('OpensoftCodeConversationBundle:Project')->findOneBy(array('name' => $projectName)));
        } else {
            $projects = $em->getRepository('OpensoftCodeConversationBundle:Project')->findAll();
        }

        if (empty($projects)) {
            if ($projectName = $input->getArgument('name')) {
                $output->writeln(strtr('<error>Could not find project "%project%"</error>', array('%project%' => $projectName)));
            } else {
                $output->writeln('<error>There are no projects configured...</error>');
            }

            return 1;
        }

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder  */
        $builder = $this->getContainer()->get('opensoft_codeconversation.git.builder');
        foreach ($projects as $project) {
            $output->writeln(strtr('Synchronizing project "<info>%project%</info>...', array('%project%' => $project->getName())));

            $builder->init($project, function ($type, $buffer) use ($output) {
                if ('err' === $type) {
                    $output->write(str_replace("\n", "\nERR| ", $buffer));
                } else {
                    $output->write(str_replace("\n", "\nOUT| ", $buffer));
                }
            });

            $this->synchronizeBranches($em, $project, $builder);

            $em->flush();

            $output->writeln('');
            $output->writeln(strtr('Synchronization complete "<info>%project%</info>"', array('%project%' => $project->getName())));
        }
    }

    protected function synchronizeBranches(EntityManager $em, Project $project, Builder $builder)
    {
        $knownBranches = $project->getBranches();
        $remoteBranches = $builder->fetchRemoteBranches();

        foreach ($knownBranches as $knownBranch) {
            if (in_array($knownBranch->getName(), $remoteBranches)) {
                // Remove knownBranch->getName from remoteBranches by value
                $remoteBranches = array_values(array_diff($remoteBranches,array($knownBranch->getName())));
                continue;
            }

            $knownBranch->setEnabled(false);
            $em->persist($knownBranch);
        }

        foreach ($remoteBranches as $newBranch) {
            // ignore origin/HEAD pointer
            if (strpos($newBranch, 'origin/HEAD') === 0) {
                continue;
            }

            $branch = new Branch();
            $branch->setProject($project);
            $branch->setName($newBranch);

            $em->persist($branch);
        }
    }
}
