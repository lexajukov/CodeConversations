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
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequest;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequestTimeline;
use Opensoft\Bundle\CodeConversationBundle\Entity\CommitComment;

/**
 * @ParamConverter("project", class="Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface")
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
     * @Route("/{projectSlug}/redirect")
     * @Method("POST")
     */
    public function redirectAction(ProjectInterface $project)
    {
        $branchName = $this->getRequest()->get('branchName');
        return $this->redirect($this->generateUrl('opensoft_codeconversation_project_show_1', array('projectSlug' => $project->getSlug(), 'branchName' => $branchName)));
    }

    /**
     * @Route("/{projectSlug}")
     * @Route("/{projectSlug}/tree/{remote}/{branchName}")
     * @Template()
     */
    public function showAction(ProjectInterface $project, $remote = null, $branchName = null)
    {
        $em = $this->get('doctrine')->getEntityManager();
        
        if ($branchName !== null && $remote !== null) {
            /** @var \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $branch  */
            $branch = $em->getRepository('OpensoftCodeConversationBundle:Branch')->findOneByName($remote . '/' . $branchName);
        } else {
            $branch = $project->getHeadBranch();
        }

        $openPullRequests = $em->getRepository('OpensoftCodeConversationBundle:PullRequest')->findBy(array('project' => $project->getId(), 'status' => PullRequest::STATUS_OPEN), array('createdAt' => 'DESC'));

        return array('project' => $project, 'branch' => $branch, 'openPullRequests' => $openPullRequests);
    }
    /**
     * @Route("/{projectSlug}/commits")
     * @Route("/{projectSlug}/tree/{branchName}/commits", requirements={"branchName" = ".+"})
     * @Template()
     */
    public function commitsAction(ProjectInterface $project, $branchName = null)
    {
        $em = $this->get('doctrine')->getEntityManager();

        if ($branchName !== null) {
            /** @var \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $branch  */
            $branch = $em->getRepository('OpensoftCodeConversationBundle:Branch')->findOneByName($branchName);
        } else {
            $branch = $project->getHeadBranch();
        }

//        $openPullRequests = $em->getRepository('OpensoftCodeConversationBundle:PullRequest')->findBy(array('project' => $project->getId(), 'status' => PullRequest::STATUS_OPEN), array('createdAt' => 'DESC'));

        return array('project' => $project, 'branch' => $branch);
    }

    /**
     * @Route("/{projectSlug}/commit/{sha1}")
     * @Template()
     */
    public function viewCommitAction(ProjectInterface $project, $sha1)
    {
        $em = $this->get('doctrine')->getEntityManager();

        $commit = $project->getCommit($sha1);

        $form = $this->createForm(new CommitCommentFormType(), new CommitComment());
        $comments = $em->getRepository('OpensoftCodeConversationBundle:CommitComment')->findBy(array('commitSha1' => $sha1));
        
        return array('commit' => $commit, 'project' => $project, 'form' => $form->createView(), 'comments' => $comments);
    }

    /**
     * @Route("/{projectSlug}/commit/{sha1}/{filepath}", requirements={"filepath" = ".+"})
     * @Template()
     */
    public function fileAction(ProjectInterface $project, $sha1, $filepath)
    {
        return array('file' => $project->getFileAtCommit($sha1, $filepath));
    }
}
