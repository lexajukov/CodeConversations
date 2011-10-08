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

        return array('project' => $project, 'recentCommits' => $recentCommits, 'branch' => $branch);
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
     * @Route("/project/{slug}/pulls/new")
     * @ParamConverter("project", class="OpensoftCodeConversationBundle:Project")
     * @Template()
     */
    public function createPullRequestAction(Project $project)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $pullRequest = new PullRequest();
        $pullRequest->setProject($project);
        $pullRequest->setCreatedAt(new \DateTime());
        $pullRequest->setInitiatedBy($this->container->get('security.context')->getToken()->getUser());
        $pullRequest->setStatus(PullRequest::STATUS_OPEN);

        $form = $this->createForm(new PullRequestFormType($project), $pullRequest);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em->persist($pullRequest);
                $em->flush();

                $this->get('session')->setFlash('success', 'Pull request engaged.');

                return $this->redirect($this->generateUrl('opensoft_codeconversation_project_viewpullrequest', array('pullId' => $pullRequest->getId(), 'slug' => $project->getSlug())));
            }
        }

        return array('project' => $project, 'form' => $form->createView());
    }

    /**
     * @Route("/project/{slug}/pull/{pullId}")
     * @ParamConverter("project", class="OpensoftCodeConversationBundle:Project")
     * @Template()
     */
    public function viewPullRequestAction(Project $project, $pullId)
    {
        $em = $this->getDoctrine()->getEntityManager();

        // TODO - find a better way...
        /** @var \Opensoft\Bundle\CodeConversationBundle\Entity\PullRequest $pullRequest  */
        $pullRequest = $em->getRepository('OpensoftCodeConversationBundle:PullRequest')->find($pullId);
        if (!$pullRequest || $pullRequest->getProject()->getId() != $project->getId()) {
            throw $this->createNotFoundException("Could not find pull request '$pullId' for " . $project->getName());
        }

        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder  */
        $builder = $this->get('opensoft_codeconversation.git.builder');
        $builder->init($project);
//
//        print_r($project->getName());
//        die();

        $mergeBase = $builder->mergeBase($pullRequest->getSourceBranch()->getName(), $pullRequest->getDestinationBranch()->getName());
//        print_r($mergeBase);
//        die();
        $diffs = $builder->unifiedDiff($mergeBase, $pullRequest->getSourceBranch()->getName());
        $commits = $builder->fetchCommits($mergeBase, $pullRequest->getSourceBranch()->getName());

        $timeline = new PullRequestTimeline();
        foreach ($commits as $commit) {
            $timeline->add($commit->getTimestamp(), $commit);
        }
        foreach ($pullRequest->getComments() as $comment)
        {
            $timeline->add($comment->getCreatedAt(), $comment);
        }

        $form = $this->createForm(new CommentFormType(), new Comment());

        return array(
            'project' => $project,
            'pullRequest' => $pullRequest,
            'form' => $form->createView(),
            'diffs' => $diffs,
            'commits' => $commits,
            'timeline' => $timeline
        );
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
