<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Git;

use Opensoft\Bundle\CodeConversationBundle\Entity\Project;
use Opensoft\Bundle\CodeConversationBundle\Entity\Branch;
use Opensoft\Bundle\CodeConversationBundle\Model\Diff;
use Opensoft\Bundle\CodeConversationBundle\Model\DiffChunk;
use Opensoft\Bundle\CodeConversationBundle\Model\Commit;
use Opensoft\Bundle\CodeConversationBundle\Exception\BuildException;
use Symfony\Component\Process\Process;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class Builder
{
    /**
     * @var \Opensoft\Bundle\CodeConversationBundle\Entity\Project
     */
    private $project;
    private $buildDir;
    private $baseBuildDir;
    private $callback = null;
    private $gitPath;
    private $gitCmds = array(
        'clone' => 'clone --progress %repo% %dir%',
        'fetch'    => 'fetch -p origin',
        'prepare'  => 'submodule update --init --recursive',
        'checkout' => 'checkout origin/%branch%',
        'reset'    => 'reset --hard %revision%',
        'log'     => 'log --pretty=format:%format% %revision% %revision2% -n %limit%',
        'log-since' => 'log --pretty=format:%format% %revision%..%revision2%',
        'show'     => 'show --pretty=format:%format% %revision%',
        'diff'     => 'diff %commit1%..%commit2% %path%',
        'diff-name-status'     => 'diff -M -C --raw %commit1% %commit2% %path%',
        'branch-list' => 'branch -r',
        'merge-base' => 'merge-base %object1% %object2%'
    );

    public function __construct($buildDir, $gitPath)
    {
        $this->gitPath = $gitPath;

        if (!is_dir($buildDir)) {
            mkdir($buildDir);
        }
        $this->baseBuildDir = $buildDir;
    }


    public function init(Project $project, $callback = null)
    {
        $this->project = $project;
        $this->callback = $callback;
        $this->buildDir = $this->baseBuildDir.'/'.substr(md5($project->getName().$project->getUrl()), 0, 6);

        if (!file_exists($this->buildDir)) {
//            $this->prepare();
            mkdir($this->buildDir, 0777, true);
            $this->execute(strtr($this->gitPath.' '.$this->gitCmds['clone'], array('%repo%' => escapeshellarg($project->getUrl()), '%dir%' => escapeshellarg($this->buildDir))), sprintf('Unable to clone repository for project "%s".', $project->getName()));
            
        }
    }

    public function fetchRemoteBranches()
    {
        $this->execute($this->gitPath.' '.$this->gitCmds['fetch'], sprintf('Unable to fetch repository for project "%s".', $this->project->getName()));

        $process = $this->execute($this->gitPath.' '.$this->gitCmds['branch-list'], sprintf('Unable to fetch branch list for project "%s".', $this->project->getName()));

        return array_map('trim', explode("\n", trim($process->getOutput())));
    }

    public function fetchRecentCommits($revision = null, $limit = null)
    {
        if (null === $revision || 'HEAD' === $revision) {
            $revision = $this->fetchHeadCommit($revision);
        }

        $format = '%H%n%s%n%cn%n%ai%n%P%n';
        $args = array('%format%' => escapeshellarg($format), '%revision%' => escapeshellarg($revision), '%limit%' => escapeshellarg($limit), '%revision2%' => null);
        $process = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['log'], $args), sprintf('Unable to get logs for project "%s".', $this->project->getName()));

//        print_r($process->getOutput());
//        die();

        $commits = array();
        $output = explode("\n", trim($process->getOutput()));
        $i = 0;
        do {
            if (!empty($output[$i])) {
                $commit = new Commit();
                $commit->setSha1($output[$i]);
                $commit->setMessage($output[$i+1]);
                $commit->setAuthor($output[$i+2]);
                $commit->setTimestamp(new \DateTime($output[$i+3]));

                // Detect merge parent
                if (strpos($output[$i+4], " ") > 0) {
                    $merge = explode(" ", $output[$i+4]);
                    $commit->setParents($merge);
                } else {
                    $commit->addParent($output[$i+4]);
                }

                $commits[] = $commit;
                $i += 6;
            } else {
                $i++;
            }
        } while ($i <= count($output));

        return $commits;
    }

    public function fetchCommits($revision = null, $revision2 = null, $limit = null)
    {
        if (null === $revision || 'HEAD' === $revision) {
            $revision = $this->fetchHeadCommit($revision);
        }

        $format = '%H%n%s%n%cn%n%ai%n%P%n';
        $args = array('%format%' => escapeshellarg($format), '%revision%' => escapeshellarg($revision), '%revision2%' => escapeshellarg($revision2));
        $process = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['log-since'], $args), sprintf('Unable to get logs between "%s" and "%s".', $revision, $revision2));

//        print_r($process->getOutput());
//        die();

        $commits = array();
        $output = explode("\n", trim($process->getOutput()));
        $i = 0;
        do {
            if (!empty($output[$i])) {
                $commit = new Commit();
                $commit->setSha1($output[$i]);
                $commit->setMessage($output[$i+1]);
                $commit->setAuthor($output[$i+2]);
                $commit->setTimestamp(new \DateTime($output[$i+3]));

                // Detect merge parent
                if (strpos($output[$i+4], " ") > 0) {
                    $merge = explode(" ", $output[$i+4]);
                    $commit->setParents($merge);
                } else {
                    $commit->addParent($output[$i+4]);
                }

                $commits[] = $commit;
                $i += 6;
            } else {
                $i++;
            }
        } while ($i <= count($output));

        return $commits;
    }

    public function unifiedDiff($object1, $object2, $path = null)
    {
        $failMessage = sprintf('Unable to show commit for project "%s".', $this->project->getName());
        $args = array('%commit1%' => escapeshellarg($object1), '%commit2%' => escapeshellarg($object2), '%path%' => $path);
        $process = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['diff'], $args), $failMessage);

        $output = explode("\n", trim($process->getOutput()));
//        print_r($output);
//        die();

        $diffOutput = $this->parseDiffOutput($output);

//        print_r(count($diffOutput));
//        die();

        return $diffOutput;
    }

    public function mergeBase($object1, $object2)
    {
        $failMessage = sprintf('Unable to find merge-base for "%s" and "%s".', $object1, $object2);
        $args =  array('%object1%' => escapeshellarg($object1), '%object2%' => escapeshellarg($object2));
        $process = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['merge-base'], $args), $failMessage);

        return trim($process->getOutput());
    }

    public function fetchCommit($object)
    {
        $format = '%H%n%s%n%cn%n%ai%n%P%n%b';
        $process = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['show'], array('%format%' => escapeshellarg($format), '%revision%' => escapeshellarg($object))), sprintf('Unable to show commit for project "%s".', $this->project->getName()));

        $output = explode("\n", trim($process->getOutput()));
//        print_r($output);
//        die();

        $commit = new Commit();
        $commit->setSha1($output[0]);
        $message = $output[1];
//        $commit->setMessage($output[1]);
        $commit->setAuthor($output[2]);
        $commit->setTimestamp(new \DateTime($output[3]));

        // Detect merge parent
        if (strpos($output[4], " ") > 0) {
            $merge = explode(" ", $output[4]);
            $commit->setParents($merge);
        } else {
            $commit->addParent($output[4]);
        }

        $i = 5;
        $subMessage = '';
        do {
            if (!empty($output[$i])) {
                $subMessage .= "\n" . $output[$i];
            }

            $i++;
        } while($i < (count($output) - 1) && strpos($output[$i], 'diff --git') !== 0);
        $commit->setMessage($message . "\n" . $subMessage);
//        print_r($message);
        
        if (isset($output[$i])) {
            $diffs = $this->parseDiffOutput(array_slice($output, $i));
            $commit->setFileDiffs($diffs);
        }


//        print_r($diffs);



//        $commit['diffs'] = $diffs;

//        $format .= '%N%n%b';
//        $diffProcess = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['show'], array('%format%' => escapeshellarg($format), '%revision%' => escapeshellarg($object))), sprintf('Unable to show commit for project "%s".', $this->project->getName()));
//        $commit['diff'] = trim($diffProcess->getOutput());


//        print_r($commit);

        return $commit;
    }

    /**
     * @param array 
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\Diff[]
     */
    private function parseDiffOutput(array $output)
    {
        $diff = null;
        $diffChunk = null;
        $diffChunkContent = array();

        $diffChunks = array();

        $i = 0;
        do {
            $line = $output[$i++];

            // start a new Diff object
            if (strpos($line, 'diff --git ') === 0) {
//                print_r($i . "\n");

                // Clean up old diff object, if there is one
                if (null !== $diff) {
                    if (null !== $diffChunk) {
                        if (!empty($diffChunkContent)) {
                            $diffChunk->setContent($diffChunkContent);
                        }

                        $diff->addDiffChunk($diffChunk);
                        $diffChunk = null;
                    }

//                    $commit->addFileDiff($diff);
                    $diffChunks[] = $diff;
                }

                $diff = new Diff();

                list($srcFileName, $dstFileName) = explode(" ", trim(substr($line, 11)));
                $diff->setSrcPath(substr($srcFileName, 2));
                $diff->setDstPath(substr($dstFileName,2));

//                // Parse extended header lines
                do {
                    $line = $output[$i++];

                    if (strpos($line, 'old mode ') === 0) {
                        $diff->setSrcMode(substr($line, 8));
                        $diff->setStatus(Diff::STATUS_MODIFICATION);
                    } elseif (strpos($line, 'new mode ') === 0) {
                        $diff->setDstMode(substr($line, 8));
                        $diff->setStatus(Diff::STATUS_MODIFICATION);
                    } elseif (strpos($line, 'deleted file mode') === 0) {
                        $diff->setDstMode(substr($line, 18));
                        $diff->setStatus(Diff::STATUS_DELETION);
                    } elseif (strpos($line, 'new file mode ') === 0) {
                        $diff->setDstMode(substr($line, 14));
                        $diff->setStatus(Diff::STATUS_ADDITION);
                    } elseif (strpos($line, 'copy from ') === 0) {
                        $diff->setSrcPath(substr($line, 10));
                        $diff->setStatus(Diff::STATUS_COPY);
                    } elseif (strpos($line, 'copy to ') === 0) {
                        $diff->setDstPath(substr($line, 8));
                        $diff->setStatus(Diff::STATUS_COPY);
                    } elseif (strpos($line, 'rename from ') === 0) {
                        $diff->setSrcPath(substr($line, 12));
                        $diff->setStatus(Diff::STATUS_RENAMING);
                    } elseif (strpos($line, 'rename to ') === 0) {
                        $diff->setDstPath(substr($line, 10));
                        $diff->setStatus(Diff::STATUS_RENAMING);
                    } elseif (strpos($line, 'index ') === 0) {
                        if (strpos(substr($line, 6), ' ') > 0) {
//                            print_r($line);
//                            print_r(substr($line, 6));
//                            print_r(explode(" ", substr($line, 6)));
//                            die();
                            list($hash, $mode) = explode(" ", substr($line, 6));
                            $diff->setSrcMode($mode);
                            $diff->setDstMode($mode);
                            $diff->setStatus(Diff::STATUS_MODIFICATION);
                        } else {
                            $hash = substr($line, 6);
                        }
                        list($srcHash, $dstHash) = explode("..", $hash);

                        $diff->setSrcSha1($srcHash);
                        $diff->setDstSha1($dstHash);
                    }
                } while ($i < count($output) && strpos($output[$i], '---') !== 0);
//
//                 Parse from-file/to-file header
                if ($i < count($output)) {
                    do {
                        $line = $output[$i++];

                        if (strpos($line, '--- ') === 0) {
                            $diff->setSrcPath(substr($line, 6));
                        } elseif (strpos($line, '+++ ') === 0 ) {
                            $diff->setDstPath(substr($line, 6));
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
                    $diff->addDiffChunk($diffChunk);
                }

                $diffChunk = new DiffChunk();
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

        if (null !== $diff) {
            if (null !== $diffChunk) {
                if (!empty($diffChunkContent)) {
                    $diffChunk->setContent($diffChunkContent);
                }

                $diff->addDiffChunk($diffChunk);
            }

//            $commit->addFileDiff($diff);
            $diffChunks[] = $diff;
        }

        return $diffChunks;
    }

    public function fetchHeadCommit($revision = null)
    {
        if (null === $revision || 'HEAD' === $revision) {
            $revision = null;
            if (file_exists($file = $this->buildDir.'/.git/HEAD')) {
                $revision = trim(file_get_contents($file));
                if (0 === strpos($revision, 'ref: ')) {
                    if (file_exists($file = $this->buildDir.'/.git/'.substr($revision, 5))) {
                        $revision = trim(file_get_contents($file));
                    } else {
                        $revision = null;
                    }
                }
            }

            if (null === $revision) {
                throw new BuildException(sprintf('Unable to get HEAD for branch "%s" for project "%s".', $this->project->getHeadBranch(), $this->project));
            }
        }

        return $revision;
    }

    public function diff($object1, $object2 = null, $path = null)
    {
        $args = array('%commit1%' => escapeshellarg($object1), '%commit2%' => null, '%path%' => null);

        if (null !== $object2) {
            $args['%commit2%'] = $object2;
        }
        if (null !== $path) {
            $args['%path%'] = $path;
        }
        $process = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['diff-name-status'], $args), strtr("Could not find diff for %object1%", array('%object1%' => $object1)));

        $files = explode("\n", trim($process->getOutput()));
        print_r($files);


        $diffs = array();
        foreach ($files as $file) {
            $fileDef = explode("\t", $file);
            $fileDef[0] = explode(" ", substr($fileDef[0], 1));

            $diff = new Diff();
            $diff->srcMode = $fileDef[0][0];
            $diff->dstMode = $fileDef[0][1];
            $diff->srcSha1 = $fileDef[0][2];
            $diff->dstSha1 = $fileDef[0][3];
            $diff->status  = substr($fileDef[0][4], 0, 1);
            $diff->statusScore = substr($fileDef[0][4], 1);
            $diff->srcPath = $fileDef[1];

            if (isset($fileDef[2])) {
                $diff->dstPath = $fileDef[2];
            }

            print_r($diff);

            $diffs[] = $diff;
        }

        return $diffs;
    }

    private function execute($command, $failMessage)
    {
        if (null !== $this->callback) {
            call_user_func($this->callback, 'out', sprintf("Running \"%s\"\n", $command));
        }
//        print_r($command."\n");
        $process = new Process($command, $this->buildDir);
        $process->setTimeout(3600);
        $process->run($this->callback);
        if (!$process->isSuccessful()) {
            throw new BuildException($failMessage);
        }

        return $process;
    }

}
