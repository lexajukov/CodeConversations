<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

/**
 * Remote Manager Interface
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface BranchManagerInterface
{
    /**
     * @return BranchInterface
     */
    public function createBranch();

    /**
     * @param array $criteria
     * @return BranchInterface[]
     */
    public function findBranchesBy(array $criteria);

    /**
     * @return BranchInterface[]
     */
    public function findBranches();

    public function findEnabledBranchesByProject(ProjectInterface $project);

    /**
     * @param string $projectName
     * @param string $remoteName
     * @param string $branchName
     */
    public function findBranchByProjectNameAndRemoteNameAndBranchName($projectName, $remoteName, $branchName);

    /**
     * @param BranchInterface $branch
     */
    public function updateBranch(BranchInterface $branch);

    /**
     * @param BranchInterface $branch
     */
    public function deleteBranch(BranchInterface $branch);

    /**
     * @return string
     */
    public function getClass();
}
