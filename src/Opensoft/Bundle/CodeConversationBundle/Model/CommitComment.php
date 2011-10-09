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
class CommitComment extends Comment
{

    /**
     * @var string
     */
    protected $commitSha1;

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
     * @return string
     */
    public function getClass()
    {
        return get_class($this);
    }


}
