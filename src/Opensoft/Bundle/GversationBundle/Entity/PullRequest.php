<?php
/*
 *
 */


namespace Opensoft\Bundle\GversationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="pull_requests")
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
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="pullRequest")
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
}
