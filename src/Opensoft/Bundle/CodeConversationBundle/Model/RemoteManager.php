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
abstract class RemoteManager implements RemoteManagerInterface
{
    public function createRemote(ProjectInterface $project)
    {
        $class = $this->getClass();

        /** @var \Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface $remote  */
        $remote = new $class();
        $remote->setProject($project);

        return $remote;
    }
}
