<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\CodeConversationBundle\Notification;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as TemplateEngine;

/**
 * Basic notifier class
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class Notifier 
{
    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var \Swift_Mailer $mailer
     */
    protected $mailer;

    /**
     * @param \Swift_Mailer $mailer
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     */
    public function __construct(\Swift_Mailer $mailer, TemplateEngine $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * Notify a set of users of some event
     *
     * Uses templating layer to construct emails
     *
     * @param $users
     * @param $subject
     * @param $templateName
     * @param $templateParams
     * @param $contentType
     * @param $from
     */
    public function notify($users, $subject, $templateName, $templateParams, $contentType = 'text/html', $from = array('code@opensoftdev.com' => 'Code Conversations'))
    {
        if (!is_array($users)) {
            $users = array($users);
        }

        $tos = array();

        foreach ($users as $user) {
            $tos[] = $user->getEmailCanonical();
        }

        $email = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($from)
                ->setTo($tos)
                ->setBody($this->templating->render($templateName, $templateParams), $contentType);

        $this->mailer->send($email);
    }
}
