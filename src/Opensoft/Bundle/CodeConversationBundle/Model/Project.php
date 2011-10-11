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
     * @var Remote[]
     */
    protected $remotes;

    /**
     * @var PullRequest[]
     */
    protected $pullRequests;

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

    public function setPullRequests($pullRequests)
    {
        $this->pullRequests = array();
        foreach ($pullRequests as $pullRequest) {
            $this->addPullRequest($pullRequest);
        }
    }

    public function addPullRequest(PullRequestInterface $pullRequest)
    {
        $this->pullRequests[] = $pullRequest;
    }

    public function getPullRequests()
    {
        return $this->pullRequests;
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
            'route' => 'opensoft_codeconversation_project_show',
            'parameters' => array(
                'projectSlug' => $this->getSlug()
            )
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function setRemotes($remotes)
    {
        $this->remotes = array();
        foreach ($remotes as $remote) {
            $this->addRemote($remote);
        }
    }

    public function addRemote(RemoteInterface $remote)
    {
        $this->remotes[] = $remote;
    }

    public function getRemotes()
    {
        return $this->remotes;
    }
}
