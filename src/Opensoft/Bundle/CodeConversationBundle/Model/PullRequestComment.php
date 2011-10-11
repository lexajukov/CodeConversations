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

    /**
     * Return an array for the form
     *
     * array(
     *   'route' => $routeName,
     *   'parameters' => array(key => value, ...)
     * )
     *
     * @return array
     */
    public function getAbsolutePathParams()
    {
        return array(
            'route' => 'opensoft_codeconversation_pullrequest_view',
            'parameters' => array(
                'projectSlug' => $this->getPullRequest()->getProject()->getSlug(),
                'pullId' => $this->getPullRequest()->getId()
            )
        );
    }

    public function __toString()
    {
        return $this->getId();
    }

}
