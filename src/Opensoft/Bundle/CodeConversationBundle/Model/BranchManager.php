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
abstract class BranchManager implements BranchManagerInterface
{

    public function createBranch()
    {
        $class = $this->getClass();

        return new $class();
    }


}
