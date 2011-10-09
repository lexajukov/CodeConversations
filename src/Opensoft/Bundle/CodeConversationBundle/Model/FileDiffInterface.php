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
interface FileDiffInterface 
{
    public function setDstMode($dstMode);

    public function getDstMode();

    public function setDstPath($dstPath);

    public function getDstPath();

    public function setDstSha1($dstSha1);

    public function getDstSha1();

    public function setSrcMode($srcMode);

    public function getSrcMode();

    public function setSrcPath($srcPath);

    public function getSrcPath();

    public function setSrcSha1($srcSha1);

    public function getSrcSha1();

    public function setStatus($status);

    public function getStatus();

    public function setStatusScore($statusScore);

    public function getStatusScore();

    public function setDiffChunks(array $diffChunks);

    public function addDiffChunk(DiffChunk $diffChunk);

    public function getDiffChunks();
}
