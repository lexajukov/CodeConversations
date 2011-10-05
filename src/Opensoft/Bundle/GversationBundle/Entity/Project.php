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
     * @ORM\Column(type="string")
     */
    protected $name;

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
     */
    protected $pullRequests;
}
