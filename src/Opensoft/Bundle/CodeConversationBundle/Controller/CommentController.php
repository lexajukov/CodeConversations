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
use Opensoft\Bundle\CodeConversationBundle\Entity\PullRequestComment;
use Opensoft\Bundle\CodeConversationBundle\Entity\CommitComment;

class CommentController extends Controller
{
    /**
     * @Route("/project/{slug}/pull/{id}/comment/new")
     * @Method("POST")
     * @ParamConverter("project", class="OpensoftCodeConversationBundle:Project")
     * @ParamConverter("pullRequest", class="OpensoftCodeConversationBundle:PullRequest")
     * @Template("OpensoftCodeConversationsBundle:Default:viewPullRequest")
     */
    public function postPrCommentAction(Project $project, PullRequest $pullRequest)
    {
        $comment = new PullRequestComment();
        $comment->setPullRequest($pullRequest);
        $comment->setCreatedAt(new \DateTime());
        $comment->setAuthor($this->container->get('security.context')->getToken()->getUser());

        /** @var \Symfony\Component\Form\Form $form  */
        $form = $this->createForm(new CommentFormType(), $comment);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                if ($request->get('close')) {
                    $pullRequest->setStatus(PullRequest::STATUS_CLOSED);
                    $em->persist($pullRequest);
                    $this->get('session')->setFlash('success', 'This pull request was closed.');
                } elseif ($request->get('reopen')) {
                    $pullRequest->setStatus(PullRequest::STATUS_OPEN);
                    $em->persist($pullRequest);
                    $this->get('session')->setFlash('success', 'This pull request was reopened.');
                } else {
                    $this->get('session')->setFlash('success', 'Your comment was added to this pull request.');
                }

                $em->persist($comment);
                $em->flush();

                return $this->redirect($this->generateUrl('opensoft_codeconversation_project_viewpullrequest', array('pullId' => $pullRequest->getId(), 'slug' => $project->getSlug())));
            }
        }

        return array('project' => $project, 'pullRequest' => $pullRequest, 'form' => $form->createView());
    }


    /**
     * @Route("/project/{slug}/commit/{sha1}/comment/new")
     * @ParamConverter("project", class="OpensoftCodeConversationBundle:Project")
     * @Method("POST")
     * @Template("OpensoftCodeConversationsBundle:Default:viewComment")
     */
    public function postCommitCommentAction(Project $project, $sha1)
    {
        /** @var \Opensoft\Bundle\CodeConversationBundle\Git\Builder $builder  */
        $builder = $this->get('opensoft_codeconversation.git.builder');
        $builder->init($project);

        $commit = $builder->fetchCommit($sha1);

        if (!$commit) {
            throw $this->createNotFoundException("Commit $sha1 could not be found");
        }

        $comment = new CommitComment();
        $comment->setCommitSha1($sha1);
        $comment->setCreatedAt(new \DateTime());
        $comment->setAuthor($this->container->get('security.context')->getToken()->getUser());

        /** @var \Symfony\Component\Form\Form $form  */
        $form = $this->createForm(new CommentFormType(), $comment);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $em->persist($comment);
                $em->flush();

                $this->get('session')->setFlash('success', 'Your comment was added to commit '.$sha1.'.');

                return $this->redirect($this->generateUrl('opensoft_codeconversation_project_viewcommit', array('sha1' => $sha1, 'slug' => $project->getSlug())));
            }
        }
        $comments = $em->getRepository('OpensoftCodeConversationBundle:CommitComment')->findBy(array('commitSha1' => $sha1));

        return array('commit' => $commit, 'project' => $project, 'form' => $form->createView(), 'comments' => $comments);
    }

}
