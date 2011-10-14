<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\PullRequestFormType;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\CommitCommentFormType;
use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\BranchInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequest;
use Opensoft\Bundle\CodeConversationBundle\Entity\CommitComment;
use GitRuntimeException;

/**
 *
 */
class ProjectController extends Controller
{
    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\ProjectManagerInterface
     */
    public function getProjectManager()
    {
        return $this->container->get('opensoft_codeconversation.manager.project');
    }

    /**
     * @Route("/project")
     * @Template()
     */
    public function dropdownMenuAction()
    {
        return array(
            'projects' => $this->getProjectManager()->findProjects()
        );
    }

    /**
     * @Route("/project-header/{projectName}/tree/{branchName}")
     * @Template()
     */
    public function headerAction(ProjectInterface $project, BranchInterface $branch = null)
    {
        if (null === $branch) {
            $branch = $project->getDefaultRemote()->getHeadBranch();
        }
        
        /** @var \Opensoft\Bundle\CodeConversationBundle\Model\BranchManagerInterface $branchManager  */
        $branchManager = $this->container->get('opensoft_codeconversation.manager.branch');

        $em = $this->get('doctrine')->getEntityManager();
        $openPullRequests = $em->getRepository('OpensoftCodeConversationBundle:PullRequest')->findBy(array('project' => $project->getId(), 'status' => PullRequest::STATUS_OPEN), array('createdAt' => 'DESC'));

        return array(
            'project' => $project,
            'branch' => $branch,
            'enabledBranches' => $branchManager->findEnabledBranchesByProject($project),
            'openPullRequests' => $openPullRequests,
        );
    }

    /**
     * @Route("/{projectName}/redirect")
     * @Method("POST")
     */
    public function redirectAction(ProjectInterface $project)
    {
        $branchName = $this->getRequest()->get('branch');
        return $this->redirect($this->generateUrl('opensoft_codeconversation_project_show_1', array(
            'projectName' => $project->getName(),
            'branchName' => $branchName
        )));
    }

    /**
     * @Route("/{projectName}")
     * @Route("/{projectName}/tree/{branchName}")
     * @Template()
     */
    public function showAction(ProjectInterface $project, BranchInterface $branch = null)
    {
        if (null === $branch) {
            $branch = $project->getDefaultRemote()->getHeadBranch();
        }
        
        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository  */
        $repository = $this->container->get('opensoft_codeconversation.repository_manager')->getRepository($project);

        $recentCommits = $repository->getCommits($branch->getFullName(), null, 1);

        try {
            $readme = $repository->getFileAtCommit($recentCommits[0]->getId(), 'README.md');
        } catch(GitRuntimeException $e) {
            $readme = null;
        }

        $tree = $repository->getTree($branch->getFullName());



        return array(
            'project' => $project,
            'branch' => $branch,
            'tree' => $tree,
            'recentCommit' => $recentCommits[0],
            'readme' => $readme
        );
    }
    /**
     * @Route("/{projectName}/commits")
     * @Route("/{projectName}/tree/{branchName}/commits")
     * @Template()
     */
    public function commitsAction(ProjectInterface $project, BranchInterface $branch = null)
    {
        if (null === $branch) {
            $branch = $project->getDefaultRemote()->getHeadBranch();
        }

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository  */
        $repository = $this->container->get('opensoft_codeconversation.repository_manager')->getRepository($project);

        $commits = array();
        foreach ($repository->getCommits($branch->getFullName(), null, 50) as $commit) {
            $commits[date("F j, Y", $commit->getCommittedDate()->getTimestamp())][] = $commit;
        }

//        $openPullRequests = $em->getRepository('OpensoftCodeConversationBundle:PullRequest')->findBy(array('project' => $project->getId(), 'status' => PullRequest::STATUS_OPEN), array('createdAt' => 'DESC'));

        return array(
            'project' => $project,
            'branch' => $branch,
            'commits' => $commits
        );
    }

    /**
     * @Route("/{projectName}/commit/{sha1}")
     * @Template()
     */
    public function viewCommitAction(ProjectInterface $project, $sha1)
    {
        $em = $this->get('doctrine')->getEntityManager();


        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository  */
        $repository = $this->container->get('opensoft_codeconversation.repository_manager')->getRepository($project);

        $commit = $repository->showCommit($sha1);

        $form = $this->createForm(new CommitCommentFormType(), new CommitComment());
        $comments = $em->getRepository('OpensoftCodeConversationBundle:CommitComment')->findBy(array('commitSha1' => $sha1));
        
        return array('commit' => $commit, 'project' => $project, 'form' => $form->createView(), 'comments' => $comments);
    }

    /**
     * @Route("/{projectName}/commit/{sha1}/{filepath}", requirements={"filepath" = ".+"})
     * @Template("OpensoftCodeConversationBundle:Project:show.html.twig")
     */
    public function fileAction(ProjectInterface $project, $sha1, $filepath, RemoteInterface $remote = null, BranchInterface $branch = null)
    {
        if (null === $remote) {
            $remote = $project->getDefaultRemote();
        }

        if (null === $branch) {
            $branch = $remote->getHeadBranch();
        }

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository  */
        $repository = $this->container->get('opensoft_codeconversation.repository_manager')->getRepository($project);
        $recentCommits = $repository->getCommits($branch->getFullName(), null, 1);

        return array(
            'project' => $project,
            'remote' => $remote,
            'branch' => $branch,
            'recentCommit' => $recentCommits[0],
            'filepath' => explode("/", $filepath),
            'file' => explode("\n", $repository->getFileAtCommit($sha1, $filepath)));
    }



    /**
     * @Route("/{projectName}/tree/{branchName}/{filepath}", requirements={"filepath" = ".+"})
     * @Template("OpensoftCodeConversationBundle:Project:show.html.twig")
     */
    public function treeAction(ProjectInterface $project, BranchInterface $branch = null, $filepath = null)
    {
        if (null === $branch) {
            $branch = $project->getDefaultRemote()->getHeadBranch();
        }

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository  */
        $repository = $this->container->get('opensoft_codeconversation.repository_manager')->getRepository($project);
        $recentCommits = $repository->getCommits($branch->getFullName(), null, 1);

        return array(
            'project' => $project,
            'branch' => $branch,
            'recentCommit' => $recentCommits[0],
            'filepath' => explode("/", $filepath),
            'tree' => $repository->getTree($branch->getFullName(), $filepath)
        );
    }

    /**
     * @Route("/{projectName}/blob/{branchName}/{filepath}", requirements={"filepath" = ".+"})
     * @Template("OpensoftCodeConversationBundle:Project:show.html.twig")
     */
    public function blobAction(ProjectInterface $project, BranchInterface $branch = null, $filepath)
    {
        if (null === $branch) {
            $branch = $project->getDefaultRemote()->getHeadBranch();
        }

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository  */
        $repository = $this->container->get('opensoft_codeconversation.repository_manager')->getRepository($project);
        $recentCommits = $repository->getCommits($branch->getFullName(), null, 1);

        return array(
            'project' => $project,
            'branch' => $branch,
            'recentCommit' => $recentCommits[0],
            'filepath' => explode("/", $filepath),
            'file' => explode("\n", $repository->getFileAtCommit($branch->getFullName(), $filepath))
        );
    }
}
