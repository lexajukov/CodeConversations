<?php
/*
 *
 */


namespace Opensoft\Bundle\CodeConversationBundle\Model;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class Branch implements BranchInterface
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
    protected $tip;
    
    /**
     * @var RemoteInterface
     */
    protected $remote;

    /**
     * @var Boolean
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
     * @param RemoteInterface $remote
     */
    public function setRemote(RemoteInterface $remote)
    {
        $this->remote = $remote;
    }

    /**
     * @return RemoteInterface
     */
    public function getRemote()
    {
        return $this->remote;
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

    public function getFullName()
    {
        return $this->getRemote()->getName().'/'.$this->name;
    }

    /**
     * @param string $tip
     */
    public function setTip($tip)
    {
        $this->tip = $tip;
    }

    /**
     * @return string
     */
    public function getTip()
    {
        return $this->tip;
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
        // only show real link for enabled branches
        if ($this->enabled) {
            $path = array(
                'route' => 'opensoft_codeconversation_project_commits_1',
                'parameters' => array(
                    'projectName' => $this->getRemote()->getProject()->getName(),
                    'branchName' => $this->getName(),
                )
            );
        } else {
            $path = array();
        }

        return $path;
    }

    public function __toString()
    {
        return $this->getFullName();
    }
}
