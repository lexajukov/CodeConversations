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
use Opensoft\Bundle\CodeConversationBundle\Entity\Comment;

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
        $comment = new Comment();
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
                } elseif ($request->get('reopen')) {
                    $pullRequest->setStatus(PullRequest::STATUS_OPEN);
                    $em->persist($pullRequest);
                }

                $em->persist($comment);
                $em->flush();

                return $this->redirect($this->generateUrl('opensoft_codeconversation_project_viewpullrequest', array('id' => $pullRequest->getId(), 'slug' => $project->getSlug())));
            }
        }

        return array('project' => $project, 'pullRequest' => $pullRequest, 'form' => $form->createView());
    }
}
