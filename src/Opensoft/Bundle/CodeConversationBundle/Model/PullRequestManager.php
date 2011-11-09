<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
abstract class PullRequestManager implements PullRequestManagerInterface
{
    public function createPullRequest()
    {
        $class = $this->getClass();

        return new $class();
    }

}
