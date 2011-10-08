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

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class Commit 
{
    private $sha1;
    private $message;
    private $timestamp;
    private $author;
    private $parents;

    private $fileDiffs;


    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setFileDiffs(array $fileDiffs)
    {
        $this->fileDiffs = array();
        foreach ($fileDiffs as $fileDiff) {
            $this->addFileDiff($fileDiff);
        }
    }

    public function addFileDiff(Diff $fileDiff)
    {
        $this->fileDiffs[] = $fileDiff;
    }

    public function getFileDiffs()
    {
        return $this->fileDiffs;
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

    public function setSha1($sha1)
    {
        $this->sha1 = $sha1;
    }

    public function getSha1()
    {
        return $this->sha1;
    }

    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
