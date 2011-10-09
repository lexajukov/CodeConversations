<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\SourceCode;

use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface RepositoryInterface 
{
    public function init(ProjectInterface $project, $callback = null);

    public function fetchRemoteBranches();

    public function fetchRecentCommits($revision = null, $limit = null);

    /**
     * @param string|null $revision
     * @param string|null $revision2
     * @param integer|null $limit
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\Commit[]
     */
    public function fetchCommits($revision = null, $revision2 = null, $limit = null);

    public function unifiedDiff($object1, $object2, $path = null);

    public function mergeBase($object1, $object2);

    public function fetchCommit($object);

    public function fetchHeadCommit($revision = null);

    public function diff($object1, $object2 = null, $path = null);

    public function fetchFileAtCommit($sha1, $filepath);
}
