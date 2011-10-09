<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\PullRequestFormType;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\CommitCommentFormType;
use Opensoft\Bundle\CodeConversationBundle\Entity\PullRequest;
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
     * @Route("/project-menu")
     * @Template()
     */
    public function dropdownMenuAction()
    {
        return array(
            'projects' => $this->getProjectManager()->findProjects()
        );
    }

    /**
     * @Route("/project/{slug}")
     * @Route("/project/{slug}/branch/{branchId}")
     * @Template()
     */
    public function showAction(ProjectInterface $project, $branchId = null)
    {
        $em = $this->get('doctrine')->getEntityManager();
//        $project = $this->getProjectManager()->findProjectBySlug($slug);

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder  */
        $builder = $this->get('opensoft_codeconversation.git.builder');
        $builder->init($project);

        if ($branchId != null) {
            /** @var \Opensoft\Bundle\CodeConversationBundle\Entity\Branch $branch  */
            $branch = $em->getRepository('OpensoftCodeConversationBundle:Branch')->find($branchId);
        } else {
            $branch = $em->getRepository('OpensoftCodeConversationBundle:Branch')->findOneByName('origin/master');
        }

        if (!$branch) {
            throw $this->createNotFoundException("Branch '$branchId' does not exist");
        }

        $recentCommits = $builder->fetchRecentCommits($branch->getName(), 15);

        $openPullRequests = $em->getRepository('OpensoftCodeConversationBundle:PullRequest')->findBy(array('project' => $project->getId(), 'status' => PullRequest::STATUS_OPEN), array('createdAt' => 'DESC'));

        return array('project' => $project, 'recentCommits' => $recentCommits, 'branch' => $branch, 'openPullRequests' => $openPullRequests);
    }

    /**
     * @Route("/project/{slug}/commit/{sha1}")
     * @Template()
     */
    public function viewCommitAction(ProjectInterface $project, $sha1)
    {
        $em = $this->get('doctrine')->getEntityManager();

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder  */
        $builder = $this->get('opensoft_codeconversation.git.builder');
        $builder->init($project);

        $commit = $builder->fetchCommit($sha1);

        $form = $this->createForm(new CommitCommentFormType(), new CommitComment());
        $comments = $em->getRepository('OpensoftCodeConversationBundle:CommitComment')->findBy(array('commitSha1' => $sha1));
        
        return array('commit' => $commit, 'project' => $project, 'form' => $form->createView(), 'comments' => $comments);
    }

    /**
     * @Route("/project/{slug}/blob/{blob}")
     * @Template()
     */
    public function blobAction(ProjectInterface $project, $blob)
    {/** @var \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder  */
        $builder = $this->get('opensoft_codeconversation.git.builder');
        $builder->init($project);

        $file = $builder->blob($blob);

        return array();
    }
}
