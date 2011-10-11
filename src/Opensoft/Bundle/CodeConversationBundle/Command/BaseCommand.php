<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

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
    public function synchronizeBranches(RemoteInterface $remote)
    {
        $knownBranches = $remote->getBranches();
        $remoteBranches = $this->sourceCodeRepository->fetchRemoteBranches($remote);

        if (!empty($knownBranches)) {
            foreach ($knownBranches as $knownBranch) {
                if (in_array($remote->getName().'/'.$knownBranch->getName(), $remoteBranches)) {
                    // Remove knownBranch->getName from remoteBranches by value
                    $remoteBranches = array_values(array_diff($remoteBranches,array($remote->getName().'/'.$knownBranch->getName())));
                    continue;
                }

                // probably shouldn't delete unknown branches that previously exists... just disable them
                $knownBranch->setEnabled(false);
            }
        }

        $defaultBranch = null;
        // known branches now only has the ones this project doesn't know about
        foreach ($remoteBranches as $newBranch) {
            // set origin/HEAD pointer as default branch
            if (strpos($newBranch, $remote->getName().'/HEAD -> ') === 0) {
                $defaultBranchName = str_replace($remote->getName(), '', substr($newBranch, strlen($remote->getName().'/HEAD -> ')));
                print_r($defaultBranchName);
                continue;
            }

            $branch = $remote->createBranch();
            $branch->setName(str_replace($remote->getName().'/', '', $newBranch));


            if ($branch->getName() === $defaultBranchName) {
                $remote->setHeadBranch($branch);
            }

            $remote->addBranch($branch);
        }
    }
}
