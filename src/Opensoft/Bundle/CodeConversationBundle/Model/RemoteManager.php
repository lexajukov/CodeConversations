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

    public function createRemote()
    {
        $class = $this->getClass();

        return new $class();
    }


}
