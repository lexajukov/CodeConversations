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
class Diff implements DiffInterface
{
    protected $fileDiffs = array();

    public function getFileDiffs()
    {
        return $this->fileDiffs;
    }

    public function addFileDiff(FileDiffInterface $fileDiff)
    {
        $this->fileDiffs[] = $fileDiff;
    }

    public function setFileDiffs(array $fileDiffs)
    {
        $this->fileDiffs = array();
        foreach ($fileDiffs as $fileDiff) {
            $this->addFileDiff($fileDiff);
        }
    }


}
