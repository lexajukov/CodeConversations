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
class FileDiff implements FileDiffInterface
{
    const STATUS_ADDITION = 'A';
    const STATUS_COPY = 'C';
    const STATUS_DELETION = 'D';
    const STATUS_MODIFICATION = 'M';
    const STATUS_RENAMING = 'R';
    const STATUS_TYPE = 'T';
    const STATUS_UNMERGED = 'U';
    const STATUS_UNKNOWN = 'X';

    public $srcMode;
    public $dstMode;
    public $srcSha1;
    public $dstSha1;

    public $status;
    public $statusScore;

    public $insertions = 0;
    public $deletions = 0;

    public $srcPath;
    public $dstPath;

    public $diffChunks = array();

    public function setDstMode($dstMode)
    {
        $this->dstMode = $dstMode;
    }

    public function getDstMode()
    {
        return $this->dstMode;
    }

    public function setDstPath($dstPath)
    {
        $this->dstPath = $dstPath;
    }

    public function getDstPath()
    {
        return $this->dstPath;
    }

    public function setDstSha1($dstSha1)
    {
        $this->dstSha1 = $dstSha1;
    }

    public function getDstSha1()
    {
        return $this->dstSha1;
    }

    public function setSrcMode($srcMode)
    {
        $this->srcMode = $srcMode;
    }

    public function getSrcMode()
    {
        return $this->srcMode;
    }

    public function setSrcPath($srcPath)
    {
        $this->srcPath = $srcPath;
    }

    public function getSrcPath()
    {
        return $this->srcPath;
    }

    public function setSrcSha1($srcSha1)
    {
        $this->srcSha1 = $srcSha1;
    }

    public function getSrcSha1()
    {
        return $this->srcSha1;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatusScore($statusScore)
    {
        $this->statusScore = $statusScore;
    }

    public function getStatusScore()
    {
        return $this->statusScore;
    }

    public function setDiffChunks(array $diffChunks)
    {
        $this->diffChunks = array();
        foreach ($diffChunks as $diffChunk) {
            $this->addDiffChunk($diffChunk);
        }
    }

    public function addDiffChunk(FileDiffChunkInterface $diffChunk)
    {
        $this->diffChunks[] = $diffChunk;

        $this->insertions += $diffChunk->getInsertions();
        $this->deletions += $diffChunk->getDeletions();
    }

    public function getDiffChunks()
    {
        return $this->diffChunks;
    }
}
