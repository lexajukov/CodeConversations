<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Git;

use PHPGit_Repository;
use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\Commit;
use Opensoft\Bundle\CodeConversationBundle\Git\Diff\DiffHeaderParser;

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
            $diff = DiffHeaderParser::parse(array_slice($output, $i));
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

        return DiffHeaderParser::parse($this->repo->git(strtr($command, $parameters)));
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
