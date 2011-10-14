<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequest;
use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\BranchInterface;
use Opensoft\Bundle\CodeConversationBundle\Git\Repository;
use Opensoft\Bundle\CodeConversationBundle\Entity\Branch;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
abstract class BaseCommand extends ContainerAwareCommand
{

    /**
     * @return void
     */
    public function synchronizeBranches(OutputInterface $output, Repository $repo, ProjectInterface $project, RemoteInterface $remote = null)
    {
        /** @var \Opensoft\Bundle\CodeConversationBundle\Model\BranchManagerInterface $branchManager  */
        $branchManager = $this->getContainer()->get('opensoft_codeconversation.manager.branch');

        /** @var \Opensoft\Bundle\CodeConversationBundle\Model\RemoteManagerInterface $remoteManager  */
        $remoteManager = $this->getContainer()->get('opensoft_codeconversation.manager.remote');

        /** @var \Opensoft\Bundle\CodeConversationBundle\Model\PullRequestManagerInterface $pullRequestManager  */
        $pullRequestManager = $this->getContainer()->get('opensoft_codeconversation.manager.pull_request');

        if (null === $remote) {
            $remotes = array($project->getDefaultRemote());
        } else {
            $remotes = $project->getRemotes();
        }

        foreach ($remotes as $remote) {
            $output->writeln('>> Syncing Remote <info>' . $remote->getName() . '</info>');

            $repo->fetch($remote);

            $knownBranches = $remote->getBranches();
            $remoteBranches = $repo->getRemoteBranches();

            if (!empty($knownBranches)) {
                foreach ($knownBranches as $knownBranch) {
                    if (in_array($remote->getName().'/'.$knownBranch->getName(), $remoteBranches)) {
                        // Remove knownBranch->getName from remoteBranches by value
                        $remoteBranches = array_values(array_diff($remoteBranches, array($knownBranch->getFullName())));

                        $output->writeln('>>> <comment>'.$knownBranch->getFullName().'</comment> already being tracked');

                        $tip = $repo->getTip($knownBranch->getFullName());

                        if ($knownBranch->getTip() != $tip) {
                            $this->recordBranchActivity($output, $repo, $knownBranch, $tip);
                        }

                        $knownBranch->setTip($tip);
                        $branchManager->updateBranch($knownBranch);

                        continue;
                    } else {
                        $output->writeln('>>> <error>'.$knownBranch->getFullName().'</error> deleted');
                        // probably shouldn't delete unknown branches that previously exists... just disable them
                        $knownBranch->setEnabled(false);

                        $branchManager->updateBranch($knownBranch);

                        // if this branch being deleted is part of any pull requests... close those requests
                        foreach ($pullRequestManager->findPullRequestBy(array('headBranch' => $knownBranch->getId())) as $pullRequest) {
                            $pullRequest->setStatus(PullRequest::STATUS_CLOSED);
                            $pullRequestManager->updatePullRequest($pullRequest);
                        }
                        foreach ($pullRequestManager->findPullRequestBy(array('baseBranch' => $knownBranch->getId())) as $pullRequest) {
                            $pullRequest->setStatus(PullRequest::STATUS_CLOSED);
                            $pullRequestManager->updatePullRequest($pullRequest);
                        }
                    }
                }
            }

            $defaultBranch = null;
            $defaultBranchName = null;
            // known branches now only has the ones this project doesn't know about
            foreach ($remoteBranches as $newBranch) {
                // set origin/HEAD pointer as default branch
                if (strpos($newBranch, $remote->getName().'/HEAD -> ') === 0) {
                    $defaultBranchName = str_replace($remote->getName(), '', substr($newBranch, strlen($remote->getName().'/HEAD -> ')));
                    continue;
                }

                $branch = $branchManager->createBranch();
                $branch->setName(str_replace($remote->getName().'/', '', $newBranch));
                $branch->setRemote($remote);
                $branch->setTip($repo->getTip($newBranch));

                $branchManager->updateBranch($branch);

                $output->writeln('>>> <info>'.$remote->getName().'/'.$branch->getName().'</info> now tracking');

                if (null !== $defaultBranchName && $branch->getName() === $defaultBranchName) {
                    $remote->setHeadBranch($branch);
                }

                $remote->addBranch($branch);
            }

            $remoteManager->updateRemote($remote);
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repo
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\BranchInterface $branch
     * @param string $newTip
     */
    public function recordBranchActivity(OutputInterface $output, Repository $repo, BranchInterface $branch, $newTip)
    {
        // loop through commits from branch->getTip() to $newTip
        $commits = $repo->getCommits($branch->getTip(), $newTip);
        if (!empty($commits)) {

            /** @var \Redpanda\Bundle\ActivityStreamBundle\Entity\ActionManager $activityManager  */
            $activityManager = $this->getContainer()->get('activity_stream.action_manager');

            /** @var \FOS\UserBundle\Entity\UserManager $userManager  */
            $userManager = $this->getContainer()->get('fos_user.user_manager');

            $project = $branch->getRemote()->getProject();

            $userCommits = array();
            foreach ($commits as $commit) {

                $user = $userManager->findUserBy(array('gitAlias' => $commit->getCommitterName()));

                if ($user) {

                    $username = $user->getUsername();

                    $userCommits[$username]['user'] = $user;
                    if (!isset($userCommits[$username]['count'])) {
                        $userCommits[$username]['count'] = 0;
                    }
                    $userCommits[$username]['count'] += 1;

                }
            }

            foreach ($userCommits as $username => $userDefinition) {
                $action = $activityManager->createAction();

                $action->setActor($userDefinition['user']);
                if ($userDefinition['count'] == 1) {
                    $action->setVerb(sprintf('pushed %d commit to', $userDefinition['count']));
                } else {
                    $action->setVerb(sprintf('pushed %d commits to', $userDefinition['count']));
                }
                $action->setTarget($branch);
                // Stupid proxy object gets put here... short circuit that....
                $action->setTargetType('Opensoft\Bundle\CodeConversationBundle\Entity\Branch');
                $action->setActionObject($project);

                $output->writeln('>>>> recording ' . $userDefinition['count'] . ' commits for user ' . $username);

                $activityManager->updateAction($action);
            }
        }
    }
}
