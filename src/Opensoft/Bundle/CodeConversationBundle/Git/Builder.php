<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Git;

use Opensoft\Bundle\CodeConversationBundle\Entity\Project;
use Opensoft\Bundle\CodeConversationBundle\Entity\Branch;
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
        'log'     => 'log --pretty=format:%format% %revision% -n %limit%',
        'show'     => 'show --pretty=format:%format% %revision%',
        'branch-list' => 'branch -r'
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

    public function fetchRecentCommits($revision = null, $limit = 10)
    {
        if (null === $revision || 'HEAD' === $revision) {
            $revision = $this->fetchHeadCommit($revision);
        }

        $format = '%H%n%h%n%s%n%ci%n%an%n';
        $process = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['log'], array('%format%' => escapeshellarg($format), '%revision%' => escapeshellarg($revision), '%limit%' => escapeshellarg($limit))), sprintf('Unable to get logs for project "%s".', $this->project->getName()));

//        print_r($process->getOutput());
//        die();

        $commits = array();
        $output = explode("\n", trim($process->getOutput()));
        $i = 0;
        do {
            if (!empty($output[$i])) {
                $commit = array();
                $commit['hash'] = $output[$i];
                $commit['short_hash'] = $output[$i+1];
                $commit['message'] = $output[$i+2];
                $commit['timestamp'] = $output[$i+3];
                $commit['author'] = $output[$i+4];

                $commits[] = $commit;
                $i += 5;
            } else {
                $i++;
            }
        } while ($i <= count($output));

        return $commits;
    }

    public function fetchCommit($object)
    {
        $format = '%H%n%h%n%s%n%ci%n%an%n';
        $process = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['show'], array('%format%' => escapeshellarg($format), '%revision%' => escapeshellarg($object))), sprintf('Unable to show commit for project "%s".', $this->project->getName()));

        $output = explode("\n", trim($process->getOutput()));

        $commit = array();
        $commit['hash'] = $output[0];
        $commit['short_hash'] = $output[1];
        $commit['message'] = $output[2];
        $commit['timestamp'] = $output[3];
        $commit['author'] = $output[4];

        $format = '%b';
        $diffProcess = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['show'], array('%format%' => escapeshellarg($format), '%revision%' => escapeshellarg($object))), sprintf('Unable to show commit for project "%s".', $this->project->getName()));
        $commit['diff'] = trim($diffProcess->getOutput());
//        print_r($commit);

        return $commit;
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

//    private function prepare($revision = null, $sync = true)
//    {
//        if (!file_exists($this->buildDir)) {
//            mkdir($this->buildDir, 0777, true);
//        }
//
//        if (!file_exists($this->buildDir.'/.git')) {
//            $this->execute(strtr($this->gitPath.' '.$this->gitCmds['clone'], array('%repo%' => escapeshellarg($this->project->getUrl()), '%dir%' => escapeshellarg($this->buildDir))), sprintf('Unable to clone repository for project "%s".', $this->project->getName()));
//        }
//
//        if ($sync) {
//
////            $this->execute($this->gitPath.' '.$this->gitCmds['prepare'], sprintf('Unable to update submodules for project "%s".', $this->project));
//        }
//
//          $this->execute(strtr($this->gitPath.' '.$this->gitCmds['checkout'], array('%branch%' => escapeshellarg($this->project->getBranch()))), sprintf('Unable to checkout branch "%s" for project "%s".', $this->project->getBranch(), $this->project));
//
//        if (null === $revision || 'HEAD' === $revision) {
//            $revision = null;
//            if (file_exists($file = $this->buildDir.'/.git/HEAD')) {
//                $revision = trim(file_get_contents($file));
//                if (0 === strpos($revision, 'ref: ')) {
//                    if (file_exists($file = $this->buildDir.'/.git/'.substr($revision, 5))) {
//                        $revision = trim(file_get_contents($file));
//                    } else {
//                        $revision = null;
//                    }
//                }
//            }
//
//            if (null === $revision) {
//                throw new BuildException(sprintf('Unable to get HEAD for branch "%s" for project "%s".', $this->project->getBranch(), $this->project));
//            }
//        }
//
////        $this->execute(strtr($this->gitPath.' '.$this->gitCmds['reset'], array('%revision%' => escapeshellarg($revision))), sprintf('Revision "%s" for project "%s" does not exist.', $revision, $this->project));
//
//        $format = '%H%n%an%n%ci%n%s%n';
//        $process = $this->execute(strtr($this->gitPath.' '.$this->gitCmds['show'], array('%format%' => escapeshellarg($format), '%revision%' => escapeshellarg($revision))), sprintf('Unable to get logs for project "%s".', $this->project->getName()));
//
//        return explode("\n", trim($process->getOutput()), 4);
//    }

    private function execute($command, $failMessage)
    {
        if (null !== $this->callback) {
            call_user_func($this->callback, 'out', sprintf("Running \"%s\"\n", $command));
        }
//        print_r($command);
        $process = new Process($command, $this->buildDir);
        $process->setTimeout(3600);
        $process->run($this->callback);
        if (!$process->isSuccessful()) {
            throw new BuildException($failMessage);
        }

        return $process;
    }

}
