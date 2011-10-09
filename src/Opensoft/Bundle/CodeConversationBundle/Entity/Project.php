<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Opensoft\Bundle\CodeConversationBundle\Model\Project as BaseProject;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class Project extends BaseProject
{

    /**
     * Ensure new branches created by this project are Entities
     *
     * @return Branch
     */
    protected function createBranch()
    {
        return new Branch();
    }
}
