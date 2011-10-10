<?php
/*
 *
 */


namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Opensoft\Bundle\CodeConversationBundle\Model\Commit;
use Opensoft\Bundle\CodeConversationBundle\Validator\BranchPoint as AssertBranchPoint;
use Opensoft\Bundle\CodeConversationBundle\Validator\OnePullRequestPerBranch as AssertOnePullRequestPerBranch;
use Opensoft\Bundle\CodeConversationBundle\Timeline\EventTimeline;
use Redpanda\Bundle\ActivityStreamBundle\Streamable\StreamableInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 *
 * @AssertBranchPoint()
 * @AssertOnePullRequestPerBranch()
 */
class PullRequest implements PullRequestInterface
{
    const STATUS_CLOSED = 0;
    const STATUS_OPEN = 1;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var Branch
     */
    protected $sourceBranch;

    /**
     * @var Branch
     */
    protected $destinationBranch;

    /**
     * @var User
     */
    protected $initiatedBy;

    /**
     * @var integer
     */
    protected $status;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Comment[]
     */
    protected $comments;

    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Model\Commit[]
     */
    protected $commits = array();

    /**
     * Github concept of a comment thread about a specific line of code
     *
     * @var
     */
//    protected $discussions;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    public function getEventTimeline()
    {
        $timeline = new EventTimeline();

        foreach ($this->getComments() as $comment) {
            $timeline->insert($comment);
        }
        foreach ($this->getCommits() as $commit) {
            $timeline->insert($commit);
        }

        return $timeline;
    }


    public function setComments(array $comments)
    {
        $this->comments = array();
        foreach ($comments as $comment) {
            $this->addComment($comment);
        }
    }

    public function addComment(CommentInterface $comment)
    {
        $this->comments[] = $comment;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\PullRequestComment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function setCommits(array $commits)
    {
        $this->commits = array();
        foreach ($commits as $commit) {
            $this->addCommit($commit);
        }
    }

    public function addCommit(CommitInterface $commit)
    {
        $this->commits[] = $commit;
    }

    public function getCommits()
    {
        if (empty($this->commits)) {
            $repo = $this->getProject()->getSourceCodeRepository();
            $mergeBase = $repo->mergeBase($this->getSourceBranch()->getName(), $this->getDestinationBranch()->getName());
            $this->setCommits($repo->fetchCommits($mergeBase, $this->getSourceBranch()->getName()));
        }

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
                'projectSlug' => $this->getProject()->getSlug(),
                'pullId' => $this->getId()
            )
        );
    }


    public function __toString()
    {
        return 'PR #' . $this->id;
    }
}
