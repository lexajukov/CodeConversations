<?php
/*
 *
 */


namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment 
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
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @var PullRequest
     *
     * @ORM\ManyToOne(targetEntity="PullRequest")
     */
    protected $pullRequest;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;


    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Entity\User $author
     */
    public function setAuthor($author)
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
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
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
}
