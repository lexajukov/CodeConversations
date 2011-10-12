<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface;
use Opensoft\Bundle\CodeConversationBundle\Git\Repository;
use Opensoft\Bundle\CodeConversationBundle\Entity\Branch;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
abstract class BaseCommand extends ContainerAwareCommand
{

    /**
     * @return void
     */
    public function synchronizeBranches(OutputInterface $output, Repository $repo, ProjectInterface $project, RemoteInterface $remote = null)
    {
        /** @var \Opensoft\Bundle\CodeConversationBundle\Model\BranchManagerInterface $branchManager  */
        $branchManager = $this->getContainer()->get('opensoft_codeconversation.manager.branch');
        /** @var \Opensoft\Bundle\CodeConversationBundle\Model\RemoteManagerInterface $remoteManager  */
        $remoteManager = $this->getContainer()->get('opensoft_codeconversation.manager.remote');

        if (null === $remote) {
            $remotes = array($project->getDefaultRemote());
        } else {
            $remotes = $project->getRemotes();
        }

        foreach ($remotes as $remote) {
            $output->writeln('>> Syncing Remote <info>' . $remote->getName() . '</info>');

            $repo->fetch($remote);

            $knownBranches = $remote->getBranches();
            $remoteBranches = $repo->getRemoteBranches();

            if (!empty($knownBranches)) {
                foreach ($knownBranches as $knownBranch) {
                    if (in_array($remote->getName().'/'.$knownBranch->getName(), $remoteBranches)) {
                        // Remove knownBranch->getName from remoteBranches by value
                        $remoteBranches = array_values(array_diff($remoteBranches, array($remote->getName().'/'.$knownBranch->getName())));

                        $output->writeln('>>> <comment>'.$remote->getName().'/'.$knownBranch->getName().'</comment> already being tracked');

                        continue;
                    } else {
                        $output->writeln('>>> <error>'.$remote->getName().'/'.$knownBranch->getName().'</error> deleted');
                        // probably shouldn't delete unknown branches that previously exists... just disable them
                        $knownBranch->setEnabled(false);
                    }
                }
            }

            $defaultBranch = null;
            $defaultBranchName = null;
            // known branches now only has the ones this project doesn't know about
            foreach ($remoteBranches as $newBranch) {
                // set origin/HEAD pointer as default branch
                if (strpos($newBranch, $remote->getName().'/HEAD -> ') === 0) {
                    $defaultBranchName = str_replace($remote->getName(), '', substr($newBranch, strlen($remote->getName().'/HEAD -> ')));
                    print_r($defaultBranchName);
                    continue;
                }

                $branch = $branchManager->createBranch();
                $branch->setName(str_replace($remote->getName().'/', '', $newBranch));
                $branch->setRemote($remote);

                $branchManager->updateBranch($branch);

                $output->writeln('>>> <info>'.$remote->getName().'/'.$branch->getName().'</info> now tracking');

                if (null !== $defaultBranchName && $branch->getName() === $defaultBranchName) {
                    $remote->setHeadBranch($branch);
                }

                $remote->addBranch($branch);
            }

            $remoteManager->updateRemote($remote);
        }
    }
}
