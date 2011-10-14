<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Symfony\Component\Validator\Constraint;

/**
 * Pull Request Manager Interface
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface PullRequestManagerInterface
{
    /**
     * @return PullRequestInterface
     */
    public function createPullRequest();

    /**
     * @param integer $id
     * @return PullRequestInterface
     */
    public function findPullRequestById($id);

    /**
     * @param array $criteria
     * @return PullRequestInterface[]
     */
    public function findPullRequestBy(array $criteria);

    /**
     * @return PullRequestInterface[]
     */
    public function findPullRequests();

    /**
     * @param PullRequestInterface $pullRequest
     */
    public function updatePullRequest(PullRequestInterface $pullRequest);

    /**
     * @param PullRequestInterface $pullRequest
     */
    public function deletePullRequest(PullRequestInterface $pullRequest);

    /**
     * @param $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     * @return Boolean
     */
    public function validateUnique($value, Constraint $constraint);

    /**
     * @return string
     */
    public function getClass();
}
