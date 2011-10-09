<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class PullRequestComment extends Comment
{


    /**
     * @var PullRequest
     */
    protected $pullRequest;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\PullRequest $pullRequest
     */
    public function setPullRequest($pullRequest)
    {
        $this->pullRequest = $pullRequest;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\PullRequest
     */
    public function getPullRequest()
    {
        return $this->pullRequest;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return get_class($this);
    }


}
