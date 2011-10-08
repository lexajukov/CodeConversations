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
        $this->timeline[$timestamp->getTimestamp()]['class'] = get_class($object);
        $this->timeline[$timestamp->getTimestamp()][get_class($object)] = $object;
    }

    public function getTimeline()
    {
        ksort($this->timeline);
        return $this->timeline;
    }
}
