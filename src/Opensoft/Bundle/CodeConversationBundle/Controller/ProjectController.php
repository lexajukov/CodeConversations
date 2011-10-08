<?php

namespace Opensoft\Bundle\CodeConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opensoft\Bundle\CodeConversationBundle\Entity\Project;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\PullRequestFormType;
use Opensoft\Bundle\CodeConversationBundle\Form\Type\CommentFormType;
use Opensoft\Bundle\CodeConversationBundle\Entity\PullRequest;
use Opensoft\Bundle\CodeConversationBundle\Model\PullRequestTimeline;
use Opensoft\Bundle\CodeConversationBundle\Entity\Comment;

class ProjectController extends Controller
{
    /**
     * @Route("/project-menu")
     * @Template()
     */
    public function dropdownMenuAction()
    {
        $em = $this->get('doctrine')->getEntityManager();
        $projects = $em->getRepository('OpensoftCodeConversationBundle:Project')->findAll();

        return array('projects' => $projects);
    }

    /**
     * @Route("/project/{slug}")
     * @Route("/project/{slug}/branch/{branchId}")
     * @ParamConverter("project", class="OpensoftCodeConversationBundle:Project")
     * @Template()
     */
    public function showAction(Project $project, $branchId = null)
    {
        $em = $this->get('doctrine')->getEntityManager();

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
     * @ParamConverter("project", class="OpensoftCodeConversationBundle:Project")
     * @Template()
     */
    public function viewCommitAction(Project $project, $sha1)
    {
        $em = $this->get('doctrine')->getEntityManager();

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder  */
        $builder = $this->get('opensoft_codeconversation.git.builder');
        $builder->init($project);

        $commit = $builder->fetchCommit($sha1);

        $form = $this->createForm(new CommentFormType(), new Comment());
        $comments = $em->getRepository('OpensoftCodeConversationBundle:CommitComment')->findBy(array('commitSha1' => $sha1));
        
        return array('commit' => $commit, 'project' => $project, 'form' => $form->createView(), 'comments' => $comments);
    }

    /**
     * @Route("/project/{slug}/blob/{blob}")
     * @ParamConverter("project", class="OpensoftCodeConversationBundle:Project")
     * @Template()
     */
    public function blobAction(Project $project, $blob)
    {/** @var \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder  */
        $builder = $this->get('opensoft_codeconversation.git.builder');
        $builder->init($project);

        $file = $builder->blob($blob);

        return array();
    }
}
