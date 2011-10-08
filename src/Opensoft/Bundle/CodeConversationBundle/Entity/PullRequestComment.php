<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 *
 * @ORM\Entity
 */
class PullRequestComment extends Comment
{


    /**
     * @var PullRequest
     *
     * @ORM\ManyToOne(targetEntity="PullRequest")
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
}
