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
    const STATUS_MERGED = 2;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $mergeBase;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var Branch
     */
    protected $baseBranch;

    /**
     * @var Branch
     */
    protected $headBranch;

    /**
     * @var User
     */
    protected $author;

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
     * Github concept of a comment thread about a specific line of code
     *
     * @var
     */
//    protected $discussions;

    /**
     * @var \DateTime
     */
    protected $createdAt;


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
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $baseBranch
     */
    public function setBaseBranch($baseBranch)
    {
        $this->baseBranch = $baseBranch;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Branch
     */
    public function getBaseBranch()
    {
        return $this->baseBranch;
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
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\User $author
     */
    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
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
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $headBranch
     */
    public function setHeadBranch($headBranch)
    {
        $this->headBranch = $headBranch;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Branch
     */
    public function getHeadBranch()
    {
        return $this->headBranch;
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
                'projectName' => $this->getProject()->getName(),
                'pullId' => $this->getId()
            )
        );
    }


    public function __toString()
    {
        return 'pull request ' . $this->id;
    }

    /**
     * @param string $mergeBase
     */
    public function setMergeBase($mergeBase)
    {
        $this->mergeBase = $mergeBase;
    }

    /**
     * @return string
     */
    public function getMergeBase()
    {
        return $this->mergeBase;
    }
}
