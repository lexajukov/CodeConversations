<?php
/*
 * This file is part of ProFIT
 *
 * Copyright (c) 2011 Farheap Solutions (http://www.farheap.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Farheap Solutions Inc. is prohibited.
 */

namespace Opensoft\Bundle\CodeConversationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 *
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
class CommitCommentFormType extends CommentFormType
{
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Opensoft\Bundle\CodeConversationBundle\Model\CommitComment');
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return 'commit_comment';
    }
}
