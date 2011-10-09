<?php
/*
 *
 */


namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class Project implements ProjectInterface
{
    /**
     * @var integer
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var Branch
     */
    protected $headBranch;

    /**
     * @var Branch[]
     */
    protected $branches;

    /**
     * @var PullRequest[]
     */
    protected $pullRequests;

    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface
     */
    protected $repo;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface $repo
     */
    public function __construct(RepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getBranches()
    {
        return $this->branches;
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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPullRequests(array $pullRequests)
    {
        $this->pullRequests = $pullRequests;
    }

    public function getPullRequests()
    {
        return $this->pullRequests;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param BranchInterface $headBranch
     */
    public function setHeadBranch(BranchInterface $headBranch)
    {
        $this->headBranch = $headBranch;
    }

    /**
     * @return BranchInterface
     */
    public function getHeadBranch()
    {
        return $this->headBranch;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function setSourceCodeRepository(RepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getSourceCodeRepository()
    {
        return $this->repo;
    }


    public function initSourceCodeRepo($callback = null)
    {
        $this->repo->init($this, $callback);
    }

    /**
     * @param string $sha1
     * @return Commit
     */
    public function getCommit($sha1)
    {
        $this->repo->init($this);

        return $this->repo->fetchCommit($sha1);
    }


}
