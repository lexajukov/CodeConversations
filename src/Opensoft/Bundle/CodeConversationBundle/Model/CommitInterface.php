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
interface CommitInterface
{
    public function setAuthorEmail($authorEmail);

    public function getAuthorEmail();

    public function setAuthorName($authorName);

    public function getAuthorName();

    public function setAuthoredDate($authoredDate);

    public function getAuthoredDate();

    public function setCommittedDate($committedDate);

    public function getCommittedDate();

    public function setCommitterEmail($committerEmail);

    public function getCommitterEmail();

    public function setCommitterName($committerName);

    public function getCommitterName();

    public function setFileDiff(FileDiffInterface $fileDiff);

    public function getFileDiff();

    public function setId($id);

    public function getId();

    public function setMessage($message);

    public function getMessage();

    public function setParents($parents);

    public function getParents();

    public function setTree($tree);

    public function getTree();
}
