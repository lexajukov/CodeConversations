<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Timeline;

/**
 * Represents an instance in time for this event's occurance
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface EventInterface 
{
    /**
     * @return \DateTime
     */
    public function getEventTimestamp();

    /**
     * @return string
     */
    public function getClass();
}
