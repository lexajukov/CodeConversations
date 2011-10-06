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
class Diff 
{
    const STATUS_ADDITION = 'A';
    const STATUS_COPY = 'C';
    CONST STATUS_DELETION = 'D';
    CONST STATUS_MODIFICATION = 'M';
    CONST STATUS_RENAMING = 'R';
    CONST STATUS_TYPE = 'T';
    CONST STATUS_UNMERGED = 'U';
    CONST STATUS_UNKNOWN = 'X';

    public $srcMode;
    public $dstMode;
    public $srcSha1;
    public $dstSha1;

    public $status;
    public $statusScore;

    public $srcPath;
    public $dstPath;


    public $content;
}
