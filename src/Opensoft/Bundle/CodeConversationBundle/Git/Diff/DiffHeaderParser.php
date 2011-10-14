<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\CodeConversationBundle\Git\Diff;

use Opensoft\Bundle\CodeConversationBundle\Model\FileDiff;
use Opensoft\Bundle\CodeConversationBundle\Model\FileDiffChunk;
use Opensoft\Bundle\CodeConversationBundle\Model\Diff;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class DiffHeaderParser
{
    /**
     * @param mixed
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\Diff
     */
    public static function parse($output)
    {
        if (!is_array($output)) {
            $output = explode("\n", $output);
        }

        $diffFile = null;
        $diffChunk = null;
        $diffChunkContent = array();

        $fileDiffs = array();

        $i = 0;
        do {
            $line = $output[$i++];

            // start a new Diff object
            if (self::isGitDiffHeader($line) || self::isGitCombinedHeader($line)) {
//                print_r($i . "\n");

                // Clean up old diff object, if there is one
                if (null !== $diffFile) {
                    if (null !== $diffChunk) {
                        if (!empty($diffChunkContent)) {
                            $diffChunk->setContent($diffChunkContent);
                        }

                        $diffFile->addFileDiffChunk($diffChunk);
                        $diffChunk = null;
                    }

//                    $commit->addFileDiff($diff);
                    $fileDiffs[] = $diffFile;
                }

                $diffFile = new FileDiff();

                if (self::isGitDiffHeader($line)) {
                    list($srcFileName, $dstFileName) = explode(" ", trim(substr($line, 11)));
                    $diffFile->setSrcPath(substr($srcFileName, 2));
                    $diffFile->setDstPath(substr($dstFileName,2));
                } elseif (self::isGitCombinedHeader($line)) {
                    $definition = explode(" ", $line);
                    $filepath = array_pop($definition);
                    $diffFile->setSrcPath($filepath);
                    $diffFile->setDstPath($filepath);
                }

//                // Parse extended header lines
                do {
                    $line = $output[$i++];

                    if (strpos($line, 'old mode ') === 0) {
                        $diffFile->setSrcMode(substr($line, 8));
                        $diffFile->setStatus(FileDiff::STATUS_MODIFICATION);
                    } elseif (strpos($line, 'new mode ') === 0) {
                        $diffFile->setDstMode(substr($line, 8));
                        $diffFile->setStatus(FileDiff::STATUS_MODIFICATION);
                    } elseif (strpos($line, 'deleted file mode') === 0) {
                        $diffFile->setDstMode(substr($line, 18));
                        $diffFile->setStatus(FileDiff::STATUS_DELETION);
                    } elseif (strpos($line, 'new file mode ') === 0) {
                        $diffFile->setDstMode(substr($line, 14));
                        $diffFile->setStatus(FileDiff::STATUS_ADDITION);
                    } elseif (strpos($line, 'copy from ') === 0) {
                        $diffFile->setSrcPath(substr($line, 10));
                        $diffFile->setStatus(FileDiff::STATUS_COPY);
                    } elseif (strpos($line, 'copy to ') === 0) {
                        $diffFile->setDstPath(substr($line, 8));
                        $diffFile->setStatus(FileDiff::STATUS_COPY);
                    } elseif (strpos($line, 'rename from ') === 0) {
                        $diffFile->setSrcPath(substr($line, 12));
                        $diffFile->setStatus(FileDiff::STATUS_RENAMING);
                    } elseif (strpos($line, 'rename to ') === 0) {
                        $diffFile->setDstPath(substr($line, 10));
                        $diffFile->setStatus(FileDiff::STATUS_RENAMING);
                    } elseif (strpos($line, 'index ') === 0) {
                        if (strpos(substr($line, 6), ' ') > 0) {
                            list($hash, $mode) = explode(" ", substr($line, 6));
                            $diffFile->setSrcMode($mode);
                            $diffFile->setDstMode($mode);
                            $diffFile->setStatus(FileDiff::STATUS_MODIFICATION);
                        } else {
                            $hash = substr($line, 6);
                        }
                        list($srcHash, $dstHash) = explode("..", $hash);

                        if (strpos($srcHash, ",")) { // better parser deserved here for detection of merge commits
                            $diffFile->setStatus(FileDiff::STATUS_MODIFICATION);
                        }

                        $diffFile->setSrcSha1($srcHash);
                        $diffFile->setDstSha1($dstHash);
                    }
                } while ($i < count($output) && strpos($output[$i], '---') !== 0);
//
//                 Parse from-file/to-file header
                if ($i < count($output)) {
                    do {
                        $line = $output[$i++];

                        if (strpos($line, '--- ') === 0) {
                            $diffFile->setSrcPath(substr($line, 6));
                        } elseif (strpos($line, '+++ ') === 0 ) {
                            $diffFile->setDstPath(substr($line, 6));
                        }
                    } while ($i < count($output) && strpos($output[$i], '@@') !== 0);
                }
            }

            // Parse for diff chunk header
//            print_r($line."\n");
            if (strpos($line, '@@') === 0) {
//                print_r("**** DIFF CHUNK DETECTED *****\n");
                if (null !== $diffChunk) {
                    if (!empty($diffChunkContent)) {
                        $diffChunk->setContent($diffChunkContent);
                    }
                    $diffFile->addFileDiffChunk($diffChunk);
                }

                $diffChunk = new FileDiffChunk();
                $diffChunk->setDescription(trim($line));
                $diffChunkContent = array();

                $diffChunkHeader = trim(str_replace(array('@@ ', ' @@'), '', substr($line, 0, strpos($line, ' @@'))));
                $array = explode(" ", $diffChunkHeader);
                $src = explode(",", substr($array[0], 1));
                $dst = explode(",", substr($array[1], 1));

                $diffChunk->setSrcStartLineNumber($src[0]);
                $diffChunk->setDstStartLineNumber($dst[0]);
            } else {
                if (null !== $diffChunk && strpos($line, '+') === 0) {
                    $diffChunk->incrementInsertions();
                } elseif (null !== $diffChunk && strpos($line, '-') === 0) {
                    $diffChunk->incrementDeletions();
                }

                $diffChunkContent[] = $line;
            }

//            $file[] = $line;



//            $i++;
        } while ($i < count($output));

        if (null !== $diffFile) {
            if (null !== $diffChunk) {
                if (!empty($diffChunkContent)) {
                    $diffChunk->setContent($diffChunkContent);
                }

                $diffFile->addFileDiffChunk($diffChunk);
            }

//            $commit->addFileDiff($diff);
            $fileDiffs[] = $diffFile;
        }

        $diff = new Diff();
        $diff->setFileDiffs($fileDiffs);

        return $diff;
    }

    protected static function isGitDiffHeader($line)
    {
        return strpos($line, 'diff --git ') === 0;
    }

    protected static function isGitCombinedHeader($line)
    {
        return strpos($line, 'diff --cc ') === 0 || strpos($line, 'diff --combined ');
    }
}
