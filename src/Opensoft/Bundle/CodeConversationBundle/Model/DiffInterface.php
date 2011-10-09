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
interface DiffInterface
{
    public function getFileDiffs();

    public function addFileDiff(FileDiffInterface $fileDiff);

    public function setFileDiffs(array $fileDiffs);
}
