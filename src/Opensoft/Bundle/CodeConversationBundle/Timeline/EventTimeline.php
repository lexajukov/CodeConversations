<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Timeline;

use Doctrine\Common\Collections\ArrayCollection;
use Opensoft\Bundle\CodeConversationBundle\Timeline\Event;
use Redpanda\Bundle\ActivityStreamBundle\Model\Action;

/**
 * An event timeline is a collection of events which maintain their ordering
 * based on the instance the event occurred
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class EventTimeline extends \SplHeap
{
    /**
     * {@inheritdoc}
     */
    public function insert($value)
    {
        if (!($value instanceof EventInterface) && !($value instanceof Action)) {
            throw new \RuntimeException("The EventTimeline heap only holds EventInterface or Action objects");
        }

        parent::insert($value);
    }

    /**
     * {@inheritdoc}
     */
    protected function compare($value1, $value2)
    {
        if ($value1 instanceof EventInterface) {
            $event1 = (float) $value1->getEventTimestamp()->getTimestamp();
        } else {
            $event1 = (float) $value1->getCreatedAt()->getTimestamp();
        }

        if ($value2 instanceof EventInterface) {
            $event2 = (float) $value2->getEventTimestamp()->getTimestamp();
        } else {
            $event2 = (float) $value2->getCreatedAt()->getTimestamp();
        }
        
        return ($event1 > $event2 ? -1 : ($event1 < $event2 ? 1 : 0));
    }
}
