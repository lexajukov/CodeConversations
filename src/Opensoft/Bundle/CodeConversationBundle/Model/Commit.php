<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Opensoft\Bundle\CodeConversationBundle\Timeline\EventInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class Commit implements CommitInterface, EventInterface
{
    private $id;
    private $tree;
    private $message;
    private $authorEmail;
    private $authorName;
    private $authoredDate;
    private $committerEmail;
    private $committerName;
    private $committedDate;
    private $parents = array();

    private $diff;


    public function setDiff(DiffInterface $diff)
    {
        $this->diff = $diff;
    }

    public function getDiff()
    {
        return $this->diff;
    }

    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
    }

    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
    }

    public function getAuthorName()
    {
        return $this->authorName;
    }

    public function setAuthoredDate(\DateTime $authoredDate)
    {
        $this->authoredDate = $authoredDate;
    }

    public function getAuthoredDate()
    {
        return $this->authoredDate;
    }

    public function setCommittedDate(\DateTime $committedDate)
    {
        $this->committedDate = $committedDate;
    }

    public function getCommittedDate()
    {
        return $this->committedDate;
    }

    public function setCommitterEmail($committerEmail)
    {
        $this->committerEmail = $committerEmail;
    }

    public function getCommitterEmail()
    {
        return $this->committerEmail;
    }

    public function setCommitterName($committerName)
    {
        $this->committerName = $committerName;
    }

    public function getCommitterName()
    {
        return $this->committerName;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setParents(array $parents)
    {
        $this->parents = array();
        foreach ($parents as $parent) {
            $this->addParent($parent);
        }
    }

    public function addParent($parent)
    {
        $this->parents[] = $parent;
    }

    public function getParents()
    {
        return $this->parents;
    }

    public function setTree($tree)
    {
        $this->tree = $tree;
    }

    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @return \DateTime
     */
    public function getEventTimestamp()
    {
        return $this->committedDate;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return get_class($this);
    }


}
