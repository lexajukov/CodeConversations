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
class FileDiffChunk implements FileDiffChunkInterface
{
    private $srcStartLineNumber;
    private $dstStartLineNumber;

    private $insertions = 0;
    private $deletions = 0;

    private $description;
    private $content;


    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setDstStartLineNumber($dstStartLineNumber)
    {
        $this->dstStartLineNumber = $dstStartLineNumber;
    }

    public function getDstStartLineNumber()
    {
        return $this->dstStartLineNumber;
    }

    public function setSrcStartLineNumber($srcStartLineNumber)
    {
        $this->srcStartLineNumber = $srcStartLineNumber;
    }

    public function getSrcStartLineNumber()
    {
        return $this->srcStartLineNumber;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDeletions($deletions)
    {
        $this->deletions = $deletions;
    }

    public function incrementDeletions($count = 1)
    {
        $this->deletions += $count;
    }

    public function decrementDeletions($count = 1)
    {
        $this->deletions -= $count;
    }

    public function getDeletions()
    {
        return $this->deletions;
    }

    public function setInsertions($insertions)
    {
        $this->insertions = $insertions;
    }

    public function incrementInsertions($count = 1)
    {
        $this->insertions += $count;
    }

    public function decrementInsertions($count = 1)
    {
        $this->insertions -= $count;
    }

    public function getInsertions()
    {
        return $this->insertions;
    }


}
