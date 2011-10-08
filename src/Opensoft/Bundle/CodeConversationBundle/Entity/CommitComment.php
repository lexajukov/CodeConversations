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
 */
class CommitComment extends Comment
{

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $commitSha1;


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
}
