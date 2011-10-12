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
     * @Route("/project-header/{projectName}/tree/{remoteName}/{branchName}")
     * @ParamConverter("project", class="Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface")
     * @Template()
     */
    public function headerAction(ProjectInterface $project, RemoteInterface $remote = null, BranchInterface $branch = null)
    {
        if (null === $remote) {
            $remote = $project->getDefaultRemote();
        }

        if (null === $branch) {
            $branch = $remote->getHeadBranch();
        }

        $em = $this->get('doctrine')->getEntityManager();
        $openPullRequests = $em->getRepository('OpensoftCodeConversationBundle:PullRequest')->findBy(array('project' => $project->getId(), 'status' => PullRequest::STATUS_OPEN), array('createdAt' => 'DESC'));

        return array(
            'project' => $project,
            'remote' => $remote,
            'branch' => $branch,
            'openPullRequests' => $openPullRequests,
        );
    }

    /**
     * @Route("/{projectName}/redirect")
     * @Method("POST")
     */
    public function redirectAction(ProjectInterface $project)
    {
        list($remoteName, $branchName) = explode("/", $this->getRequest()->get('remotebranch'));
        return $this->redirect($this->generateUrl('opensoft_codeconversation_project_show_1', array(
            'projectName' => $project->getName(),
            'remoteName' => $remoteName,
            'branchName' => $branchName
        )));
    }

    /**
     * @Route("/{projectName}")
     * @Route("/{projectName}/tree/{remoteName}/{branchName}")
     * @ParamConverter("project", class="Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface")
     * @Template()
     */
    public function showAction(ProjectInterface $project, RemoteInterface $remote = null, BranchInterface $branch = null)
    {
        $em = $this->get('doctrine')->getEntityManager();

        if (null === $remote) {
            $remote = $project->getDefaultRemote();
        }

        if (null === $branch) {
            $branch = $remote->getHeadBranch();
        }
        
//        if ($branchName !== null && $remote !== null) {
//            /** @var \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $branch  */
//            $branch = $em->getRepository('OpensoftCodeConversationBundle:Branch')->findOneByName($remote . '/' . $branchName);
//        } else {
//            $branch = $project->getDefaultRemote()->getHeadBranch();
//        }
        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository  */
        $repository = $this->container->get('opensoft_codeconversation.repository_manager')->getRepository($project);

        $recentCommits = $repository->getCommits($remote->getName().'/'.$branch->getName(), null, 1);

        try {
            $readme = $repository->getFileAtCommit($recentCommits[0]->getId(), 'README.md');
        } catch(GitRuntimeException $e) {
            $readme = null;
        }

        $tree = $repository->getTree($branch->getFullName());


        return array(
            'project' => $project,
            'remote' => $remote,
            'branch' => $branch,
            'tree' => $tree,
            'recentCommit' => $recentCommits[0],
            'readme' => $readme
        );
    }
    /**
     * @Route("/{projectName}/commits")
     * @Route("/{projectName}/tree/{remoteName}/{branchName}/commits")
     * @Template()
     */
    public function commitsAction(ProjectInterface $project, RemoteInterface $remote = null, BranchInterface $branch = null)
    {

        if (null === $remote) {
            $remote = $project->getDefaultRemote();
        }

        if (null === $branch) {
            $branch = $remote->getHeadBranch();
        }

//        $em = $this->get('doctrine')->getEntityManager();
//
//        if ($branchName !== null) {
//            /** @var \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $branch  */
//            $branch = $em->getRepository('OpensoftCodeConversationBundle:Branch')->findOneByName($branchName);
//        } else {
//            $branch = $project->getHeadBranch();
//        }


        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository  */
        $repository = $this->container->get('opensoft_codeconversation.repository_manager')->getRepository($project);

        $commits = array();
        foreach ($repository->getCommits($remote->getName().'/'.$branch->getName(), null, 50) as $commit) {
            $commits[date("F j, Y", $commit->getCommittedDate()->getTimestamp())][] = $commit;
        }

//        $openPullRequests = $em->getRepository('OpensoftCodeConversationBundle:PullRequest')->findBy(array('project' => $project->getId(), 'status' => PullRequest::STATUS_OPEN), array('createdAt' => 'DESC'));

        return array('project' => $project, 'remote' => $remote, 'branch' => $branch, 'commits' => $commits);
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
     * @Template()
     */
    public function fileAction(ProjectInterface $project, $sha1, $filepath)
    {
        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository  */
        $repository = $this->container->get('opensoft_codeconversation.repository_manager')->getRepository($project);

        return array('project' => $project, 'file' => explode("\n", $repository->getFileAtCommit($sha1, $filepath)));
    }



    /**
     * @Route("/{projectName}/tree/{remoteName}/{branchName}/{filepath}", requirements={"filepath" = ".+"})
     * @Template("OpensoftCodeConversationBundle:Project:show.html.twig")
     */
    public function treeAction(ProjectInterface $project, RemoteInterface $remote = null, BranchInterface $branch = null, $filepath = null)
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
            'filepath' => $filepath,
            'tree' => $repository->getTree($branch->getFullName(), $filepath)
        );
    }

    /**
     * @Route("/{projectName}/blob/{remoteName}/{branchName}/{filepath}", requirements={"filepath" = ".+"})
     * @Template("OpensoftCodeConversationBundle:Project:show.html.twig")
     */
    public function blobAction(ProjectInterface $project, RemoteInterface $remote = null, BranchInterface $branch = null, $filepath)
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
            'filepath' => $filepath,
            'file' => explode("\n", $repository->getFileAtCommit($branch->getFullName(), $filepath))
        );
    }
}
