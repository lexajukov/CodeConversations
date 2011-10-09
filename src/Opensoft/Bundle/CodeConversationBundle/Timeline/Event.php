<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Timeline;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
abstract class Event implements EventInterface
{
    /**
     * @var \DateTime
     */
    protected $timestamp;

    /**
     * @return \DateTime
     */
    public function getEventTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setEventTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
    }
}
