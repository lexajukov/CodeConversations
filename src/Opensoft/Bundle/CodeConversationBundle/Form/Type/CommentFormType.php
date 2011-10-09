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
abstract class CommentFormType extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('content', 'textarea')
        ;
    }


}
