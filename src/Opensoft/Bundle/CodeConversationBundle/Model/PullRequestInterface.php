<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Redpanda\Bundle\ActivityStreamBundle\Streamable\StreamableInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface PullRequestInterface extends StreamableInterface
{
    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\PullRequestComment[]
     */
    public function setComments(array $comments);

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\PullRequestComment[]
     */
    public function getComments();

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
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\User $author
     */
    public function setAuthor(UserInterface $author);

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\User
     */
    public function getAuthor();

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
