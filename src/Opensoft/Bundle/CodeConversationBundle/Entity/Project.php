<?php
/*
 *
 */


namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="projects")
 */
class Project
{
    /**
     * @var integer
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string")
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="text", unique=true)
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password;

    /**
     * @var Branch
     *
     * @ORM\ManyToOne(targetEntity="Branch")
     */
    protected $headBranch;

    /**
     * @var Branch[]
     *
     * @ORM\OneToMany(targetEntity="Branch", mappedBy="project")
     */
    protected $branches;

    /**
     * @var PullRequest[]
     *
     * @ORM\OneToMany(targetEntity="PullRequest", mappedBy="project")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    protected $pullRequests;

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

    public function setPullRequests($pullRequests)
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
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $headBranch
     */
    public function setHeadBranch(Branch $headBranch)
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


}
