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

    /**
     * @var Boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;


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
     * @param \Opensoft\Bundle\GversationBundle\Entity\Project $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return \Opensoft\Bundle\GversationBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \Boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (Boolean) $enabled;
    }

    /**
     * @return \Boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }


}
