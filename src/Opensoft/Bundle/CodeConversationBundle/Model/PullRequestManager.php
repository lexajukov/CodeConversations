<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Opensoft\Bundle\CodeConversationBundle\Git\Repository;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
abstract class PullRequestManager implements PullRequestManagerInterface
{
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }
    
    public function createPullRequest()
    {
        $class = $this->getClass();

        return new $class();
    }

}
