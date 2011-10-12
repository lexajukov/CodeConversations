<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Git;

use PHPGit_Repository;
use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\Diff;
use Opensoft\Bundle\CodeConversationBundle\Model\FileDiff;
use Opensoft\Bundle\CodeConversationBundle\Model\FileDiffChunk;
use Opensoft\Bundle\CodeConversationBundle\Model\Commit;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class Repository
{
    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface
     */
    private $project;

    /**
     * @var \PHPGit_Repository
     */
    private $repo;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface $project
     * @param \PHPGit_Repository $repo
     */
    public function __construct(ProjectInterface $project, PHPGit_Repository $repo)
    {
        $this->project = $project;
        $this->repo = $repo;
    }

    /**
     * Fetches new commits from the specified remote, or the default project
     * remote if not specified
     *
     * @param null|\Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface $remote
     */
    public function fetch(RemoteInterface $remote = null)
    {
        if (null === $remote) {
            $remote = $this->project->getDefaultRemote();
        }

        $this->repo->git(sprintf('fetch -p %s', $remote->getName()));
    }

    /**
     * @return array
     */
    public function getRemoteBranches()
    {
        return array_map('trim', explode("\n", $this->repo->git('branch -r')));
    }

    /**
     * @param string $since
     * @param string|null $until
     * @param limit|null $limit
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\Commit[]
     */
    public function getCommits($since, $until = null, $limit = null)
    {
        $command = "log --date=%dateFormat% --format=format:'%prettyFormat%' %since%";
        $parameters = array(
            '%dateFormat%' => 'iso',
            '%prettyFormat%' => '%H|%T|%an|%ae|%ad|%cn|%ce|%cd|%P|%s',
            '%since%' => $since,
        );

        if (null !== $until) {
            $command .= '..%until%';
            $parameters['%until%'] = $until;
        }

        if (null !== $limit) {
            $command .= ' -n %limit%';
            $parameters['%limit%'] = (int) $limit;
        }
//        print_r(strtr($command, $parameters));
        $output = $this->repo->git(strtr($command, $parameters));
        $commits = array();
//        print_r($output);
        if (!empty($output)) {
            foreach (explode("\n", $output) as $line) {
                $infos = explode('|', $line);
                $commit = new Commit();
                $commit->setId($infos[0]);
                $commit->setTree($infos[1]);
                $commit->setAuthorName($infos[2]);
                $commit->setAuthorEmail($infos[3]);
                $commit->setAuthoredDate(new \DateTime($infos[4]));
                $commit->setCommitterName($infos[5]);
                $commit->setCommitterEmail($infos[6]);
                $commit->setCommittedDate(new \DateTime($infos[7]));
                $commit->setParents(explode(' ',$infos[8]));
                $commit->setMessage($infos[9]);
    
                $commits[] = $commit;
            }
        }

        return $commits;
    }

    public function showCommit($object)
    {
        $command = "show --pretty=format:'%format%' %object%";
        $parameters = array('%format%' => '%H|%T|%an|%ae|%ad|%cn|%ce|%cd|%P|%s%n%b', '%object%' => $object);
        
        $output = explode("\n", $this->repo->git(strtr($command, $parameters)));

        $infos = explode('|', $output[0]);
        $commit = new Commit();
        $commit->setId($infos[0]);
        $commit->setTree($infos[1]);
        $commit->setAuthorName($infos[2]);
        $commit->setAuthorEmail($infos[3]);
        $commit->setAuthoredDate(new \DateTime($infos[4]));
        $commit->setCommitterName($infos[5]);
        $commit->setCommitterEmail($infos[6]);
        $commit->setCommittedDate(new \DateTime($infos[7]));
        $commit->setParents(explode(' ',$infos[8]));

        $message = $infos[9];
        
        $i = 1;
        $subMessage = '';
        do {
            if (!empty($output[$i])) {
                $subMessage .= "\n" . $output[$i];
            }

            $i++;
        } while($i < (count($output) - 1) && strpos($output[$i], 'diff --git') !== 0 && strpos($output[$i], 'diff --cc') !== 0);

        $commit->setMessage($message . "\n" . $subMessage);

        if (isset($output[$i])) {
            $diff = $this->parseDiffOutput(array_slice($output, $i));
            $commit->setDiff($diff);
        }

        return $commit;
    }


    public function getDiff($commit1, $commit2, $path = null)
    {
        $command = 'diff %commit1%..%commit2%';
        $parameters = array('%commit1%' => $commit1, '%commit2%' => $commit2);

        if (null !== $path) {
            $command .= ' %path%';
            $parameters['%path%'] = $path;
        }

        $output = explode("\n", $this->repo->git(strtr($command, $parameters)));
        
        return $this->parseDiffOutput($output);
    }

    public function getMergeBase($object1, $object2)
    {
        return $this->repo->git(sprintf('merge-base %s %s', $object1, $object2));
    }

    /**
     * @param string $sha1
     * @param string $filepath
     * @return string
     */
    public function getFileAtCommit($sha1, $filepath)
    {
        return $this->repo->git(sprintf('show %s:%s', $sha1, $filepath));
    }

    /**
     * @param string $revision
     * @return string
     */
    public function getTip($revision)
    {
        return $this->repo->git(sprintf('rev-list --max-count=1 %s', $revision));
    }

    public function getTree($treeish, $path = null)
    {
        $objects = array();
        $output = $this->repo->git(sprintf('ls-tree %s%s', $treeish, ':'.$path));
        if (!empty($output)) {
            foreach (explode("\n", $output) as $line) {
                $info = explode("\t", $line);
                $definition = explode(" ", $info[0]);

                $object = array();
                $object['mode'] = $definition[0];
                $object['type'] = $definition[1];
                $object['object'] = $definition[2];
                $object['file'] = $info[1];

                $objects[] = $object;
            }
        }

        return $objects;
    }


    /**
     * @param array 
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\Diff
     */
    private function parseDiffOutput(array $output)
    {
        $diffFile = null;
        $diffChunk = null;
        $diffChunkContent = array();

        $fileDiffs = array();

        $i = 0;
        do {
            $line = $output[$i++];

            // start a new Diff object
            if (strpos($line, 'diff --git ') === 0 || strpos($line, 'diff --cc') === 0) {
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

                // COMMENTED OUT LINES BREAK diff --cc (combined diff)
//                list($srcFileName, $dstFileName) = explode(" ", trim(substr($line, 11)));
//                $diffFile->setSrcPath(substr($srcFileName, 2));
//                $diffFile->setDstPath(substr($dstFileName,2));

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

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \PHPGit_Repository $repo
     */
    public function setRepo($repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return \PHPGit_Repository
     */
    public function getRepo()
    {
        return $this->repo;
    }


}
