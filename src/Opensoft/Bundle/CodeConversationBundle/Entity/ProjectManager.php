<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Opensoft\Bundle\CodeConversationBundle\Model\ProjectManager as BaseProjectManager;
use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
use Opensoft\Bundle\CodeConversationBundle\Git\Repository;
use Symfony\Component\Validator\Constraint;
use Doctrine\ORM\EntityManager;

/**
 * Entity project manager
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class ProjectManager extends BaseProjectManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Git\Repository $repository
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $class
     */
    public function __construct(Repository $repository, EntityManager $em, $class)
    {
        parent::__construct($repository);

        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * @param string $slug
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Project
     */
    public function findProjectBySlug($slug)
    {
        return $this->repository->findOneBy(array('slug' => $slug));
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Project[]
     */
    public function findProjects()
    {
        return $this->repository->findAll();
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface $project
     */
    public function deleteProject(ProjectInterface $project)
    {
        $this->em->remove($project);
        $this->em->flush();
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface $project
     * @param Boolean $andFlush
     */
    public function updateProject(ProjectInterface $project, $andFlush = true)
    {
        $this->em->persist($project);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     * @return boolean
     */
    public function validateUnique($value, Constraint $constraint)
    {
        // TODO: Implement validateUnique() method.
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

}
