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
    /**
     * @var FileDiffInterface[]
     */
    protected $fileDiffs = array();

    /**
     * @return FileDiffInterface[]
     */
    public function getFileDiffs()
    {
        return $this->fileDiffs;
    }

    /**
     * @param FileDiffInterface $fileDiff
     */
    public function addFileDiff(FileDiffInterface $fileDiff)
    {
        $this->fileDiffs[] = $fileDiff;
    }

    /**
     * @param FileDiffInterface[] $fileDiffs
     */
    public function setFileDiffs(array $fileDiffs)
    {
        $this->fileDiffs = array();
        foreach ($fileDiffs as $fileDiff) {
            $this->addFileDiff($fileDiff);
        }
    }
}
