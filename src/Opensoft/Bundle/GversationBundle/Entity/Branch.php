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
 * @ORM\Table(name="branches")
 */
class Branch 
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
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     */
    protected $project;
}
