<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Timeline;

use Doctrine\Common\Collections\ArrayCollection;
use Opensoft\Bundle\CodeConversationBundle\Timeline\Event;

/**
 * An event timeline is a collection of events which maintain their ordering
 * based on the instance the event occurred
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class EventTimeline extends \SplHeap
{
//    public function addEvent(EventInterface $event)
//    {
//        $this->insert($event);
//    }

    public function insert($value)
    {
        if (!($value instanceof EventInterface)) {
            throw new \RuntimeException("The EventTimeline heap only holds EventInterface objects");
        }

        parent::insert($value);
    }


    /**
     * {@inheritdoc}
     */
    protected function compare($value1, $value2)
    {
        $event1 = $value1->getEventTimestamp()->getTimestamp();
        $event2 = $value1->getEventTimestamp()->getTimestamp();

        return ($event1 > $event2 ? 1 : ($event1 < $event2 ? -1 : 0));
    }
}
