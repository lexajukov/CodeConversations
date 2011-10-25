<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Model;

use Symfony\Component\Validator\Constraint;

/**
 * Project Manager Interface
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface ProjectManagerInterface 
{
    /**
     * @return ProjectInterface
     */
    public function createProject();

    /**
     * @param string $name
     * @return ProjectInterface
     */
    public function findProjectByName($name);

    /**
     * @param array $criteria
     * @return ProjectInterface
     */
    public function findProjectBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param ProjectInterface $project
     */
    public function updateProject(ProjectInterface $project);

    /**
     * @param ProjectInterface $project
     */
    public function deleteProject(ProjectInterface $project);


    /**
     * @param $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     * @return Boolean
     */
    public function validateUnique($value, Constraint $constraint);

    /**
     * @return string
     */
    public function getClass();
}
