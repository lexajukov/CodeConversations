<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\CodeConversationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Opensoft\Bundle\CodeConversationBundle\Entity\Project;
use Opensoft\Bundle\CodeConversationBundle\Entity\Branch;
use Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface;
use Doctrine\ORM\EntityManager;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
abstract class BaseCommand extends ContainerAwareCommand
{
    protected function synchronizeBranches(EntityManager $em, Project $project)
    {
        $sourceCodeRepo = $project->getSourceCodeRepository();
        $knownBranches = $project->getBranches();
        $remoteBranches = $sourceCodeRepo->fetchRemoteBranches();

        if (!empty($knownBranches)) {
            foreach ($knownBranches as $knownBranch) {
                if (in_array($knownBranch->getName(), $remoteBranches)) {
                    // Remove knownBranch->getName from remoteBranches by value
                    $remoteBranches = array_values(array_diff($remoteBranches,array($knownBranch->getName())));
                    continue;
                }

                $knownBranch->setEnabled(false);
                $em->persist($knownBranch);
            }
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
