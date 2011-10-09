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
interface PullRequestInterface 
{
     public function setComments($comments);

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\PullRequestComment[]
     */
    public function getComments();

    public function setCommits(array $commits);

    public function addCommit(Commit $commit);

    public function getCommits();

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $destinationBranch
     */
    public function setDestinationBranch($destinationBranch);

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Branch
     */
    public function getDestinationBranch();

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\User $initiatedBy
     */
    public function setInitiatedBy($initiatedBy);

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\User
     */
    public function getInitiatedBy();

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\Project $project
     */
    public function setProject($project);

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Project
     */
    public function getProject();

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $sourceBranch
     */
    public function setSourceBranch($sourceBranch);

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Branch
     */
    public function getSourceBranch();

    /**
     * @param int $status
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();
}
