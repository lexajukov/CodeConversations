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
    private $parent;
    private $mergeParent;

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

    public function setMergeParent($mergeParent)
    {
        $this->mergeParent = $mergeParent;
    }

    public function getMergeParent()
    {
        return $this->mergeParent;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setSha1($sha1)
    {
        $this->sha1 = $sha1;
    }

    public function getSha1()
    {
        return $this->sha1;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
