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
class PullRequestTimeline 
{
    private $timeline = array();

    public function add(\DateTime $timestamp, $object)
    {
        $eventTimestamp = $timestamp->getTimestamp();
        if (!isset($this->timeline[$eventTimestamp])) {
            $eventTimestamp .= strtotime("now");
        }
        $this->timeline[$eventTimestamp]['class'] = get_class($object);
        $this->timeline[$eventTimestamp][get_class($object)] = $object;
    }

    public function getTimeline()
    {
        ksort($this->timeline);
        return $this->timeline;
    }
}
