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
interface FileDiffChunkInterface 
{
    public function setContent($content);

    public function getContent();

    public function setDstStartLineNumber($dstStartLineNumber);

    public function getDstStartLineNumber();

    public function setSrcStartLineNumber($srcStartLineNumber);

    public function getSrcStartLineNumber();

    public function setDescription($description);

    public function getDescription();

    public function setDeletions($deletions);

    public function incrementDeletions($count = 1);

    public function decrementDeletions($count = 1);

    public function getDeletions();

    public function setInsertions($insertions);

    public function incrementInsertions($count = 1);

    public function decrementInsertions($count = 1);

    public function getInsertions();
}
