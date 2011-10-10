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

    public function setPullRequests(array $pullRequests)
    {
        $this->pullRequests = $pullRequests;
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
     * @todo - this should get moved into the source repository?
     * @return void
     */
    public function synchronizeBranches()
    {
        $knownBranches = $this->getBranches();
        $remoteBranches = $this->repo->fetchRemoteBranches();

        if (!empty($knownBranches)) {
            foreach ($knownBranches as $knownBranch) {
                if (in_array($knownBranch->getName(), $remoteBranches)) {
                    // Remove knownBranch->getName from remoteBranches by value
                    $remoteBranches = array_values(array_diff($remoteBranches,array($knownBranch->getName())));
                    continue;
                }

                $knownBranch->setEnabled(false);
            }
        }

        $defaultBranch = null;
        // known branches now only has the ones this project doesn't know about
        foreach ($remoteBranches as $newBranch) {
            // set origin/HEAD pointer as default branch
            if (strpos($newBranch, 'origin/HEAD -> ') === 0) {
                $defaultBranchName = substr($newBranch, 15);
                print_r($defaultBranchName);
                continue;
            }

            $branch = $this->createBranch();
            $branch->setProject($this);
            $branch->setName($newBranch);


            if ($branch->getName() === $defaultBranchName) {
                $this->setHeadBranch($branch);
            }

            $this->addBranch($branch);
        }
    }


    public function getRecentCommits($object = null, $limit = 15)
    {
        $this->repo->init($this);
        return $this->repo->fetchRecentCommits($object, $limit);
    }

    protected function createBranch()
    {
        return new Branch();
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

    public function getFileAtCommit($sha1, $filepath)
    {
        $this->repo->init($this);

        return $this->repo->fetchFileAtCommit($sha1, $filepath);
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
            'parameters' => array('projectSlug' => $this->getSlug())
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
