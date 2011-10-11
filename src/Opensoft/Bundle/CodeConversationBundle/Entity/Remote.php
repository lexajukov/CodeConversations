<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Opensoft\Bundle\CodeConversationBundle\Model\Remote as BaseRemote;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class Remote extends BaseRemote
{
    /**
     * @return BranchInterface
     */
    public function createBranch()
    {
        $branch = new Branch();
        $branch->setRemote($this);
        
        return $branch;
    }
}
