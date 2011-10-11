<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Redpanda\Bundle\ActivityStreamBundle\Streamable\StreamableInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class CommitComment extends Comment implements StreamableInterface
{

    /**
     * @var string
     */
    protected $commitSha1;

    /**
     * @var ProjectInterface
     */
    protected $project;

    /**
     * @param string $commitSha1
     */
    public function setCommitSha1($commitSha1)
    {
        $this->commitSha1 = $commitSha1;
    }

    /**
     * @return string
     */
    public function getCommitSha1()
    {
        return $this->commitSha1;
    }

    /**
     * @param ProjectInterface $project
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;
    }

    /**
     * @return
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return get_class($this);
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
            'route' => 'opensoft_codeconversation_project_viewcommit',
            'parameters' => array(
                'sha1' => $this->commitSha1,
                'projectSlug' => $this->project->getSlug()
            )
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->commitSha1;
    }

}
