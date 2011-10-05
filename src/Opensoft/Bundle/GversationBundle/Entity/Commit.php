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
 * @ORM\Table(name="commits")
 */
class Commit 
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
     * @ORM\Column(type="string")
     */
    protected $hash;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $message;

    /**
     * @var PullRequest
     *
     * @ORM\ManyToOne(targetEntity="PullRequest")
     */
    protected $pullRequest;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;
}
