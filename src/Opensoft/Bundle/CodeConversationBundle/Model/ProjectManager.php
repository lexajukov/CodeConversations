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
abstract class ProjectManager implements ProjectManagerInterface
{
    
    public function createProject()
    {
        $class = $this->getClass();

        return new $class();
    }



}
