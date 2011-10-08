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

class PullRequestController extends Controller
{
    /**
     * @Route("/project/{slug}/pulls/new")
     * @ParamConverter("project", class="OpensoftCodeConversationBundle:Project")
     * @Template()
     */
    public function createAction(Project $project)
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

                return $this->redirect($this->generateUrl('opensoft_codeconversation_pullrequest_view', array('pullId' => $pullRequest->getId(), 'slug' => $project->getSlug())));
            }
        }

        return array('project' => $project, 'form' => $form->createView());
    }

    /**
     * @Route("/project/{slug}/pulls")
     * @ParamConverter("project", class="OpensoftCodeConversationBundle:Project")
     * @Template()
     */
    public function listAction(Project $project)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $pullRequests = $em->getRepository('OpensoftCodeConversationBundle:PullRequest')->findBy(array('project' => $project->getId()), array('createdAt' => 'DESC'));

        return array('project' => $project, 'pullRequests' => $pullRequests);
    }

    /**
     * @Route("/project/{slug}/pull/{pullId}")
     * @ParamConverter("project", class="OpensoftCodeConversationBundle:Project")
     * @Template()
     */
    public function viewAction(Project $project, $pullId)
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

        /** @var \Gedmo\Loggable\Entity\LogEntry[] $logs  */
        $logs = $em->getRepository('StofDoctrineExtensionsBundle:LogEntry')->findBy(array('objectClass' => get_class($pullRequest), 'objectId' => $pullRequest->getId()));
        foreach ($logs as $logEntry) {
            $timeline->add($logEntry->getLoggedAt(), $logEntry);
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
}
