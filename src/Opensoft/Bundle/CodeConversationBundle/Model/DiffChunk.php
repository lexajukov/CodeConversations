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
class DiffChunk 
{
    private $srcStartLineNumber;
    private $dstStartLineNumber;

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
}
