<?php
/*
 *
 */


namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opensoft\Bundle\CodeConversationBundle\Validator\BranchPoint as AssertBranchPoint;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="pull_requests")
 *
 * @AssertBranchPoint()
 */
class PullRequest
{
    const STATUS_CLOSED = 0;
    const STATUS_OPEN = 1;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     */
    protected $project;

    /**
     * @var Branch
     *
     * @ORM\ManyToOne(targetEntity="Branch")
     */
    protected $sourceBranch;

    /**
     * @var Branch
     *
     * @ORM\ManyToOne(targetEntity="Branch")
     */
    protected $destinationBranch;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $initiatedBy;

    /**
     * @var integer
     * 
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @var Comment[]
     *
     * @ORM\OneToMany(targetEntity="PullRequestComment", mappedBy="pullRequest")
     */
    protected $comments;

    /**
     * @var Commit[]
     *
     * @ORM\OneToMany(targetEntity="Commit", mappedBy="pullRequest")
     */
    protected $commits;

    /**
     * Github concept of a comment thread about a specific line of code
     *
     * @var
     */
//    protected $discussions;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;


    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\PullRequestComment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function setCommits($commits)
    {
        $this->commits = $commits;
    }

    public function getCommits()
    {
        return $this->commits;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $destinationBranch
     */
    public function setDestinationBranch($destinationBranch)
    {
        $this->destinationBranch = $destinationBranch;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Branch
     */
    public function getDestinationBranch()
    {
        return $this->destinationBranch;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\User $initiatedBy
     */
    public function setInitiatedBy($initiatedBy)
    {
        $this->initiatedBy = $initiatedBy;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\User
     */
    public function getInitiatedBy()
    {
        return $this->initiatedBy;
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\Project $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $sourceBranch
     */
    public function setSourceBranch($sourceBranch)
    {
        $this->sourceBranch = $sourceBranch;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Branch
     */
    public function getSourceBranch()
    {
        return $this->sourceBranch;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
