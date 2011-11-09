<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Opensoft\Bundle\CodeConversationBundle\Model\ProjectManager as BaseProjectManager;
use Opensoft\Bundle\CodeConversationBundle\Model\ProjectInterface;
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
    protected $entityRepository;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->entityRepository = $em->getRepository($class);
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * @param string $name
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Project
     */
    public function findProjectByName($name)
    {
        return $this->entityRepository->findOneBy(array('name' => $name));
    }


    /**
     * @param array $criteria
     * @return \Opensoft\Bundle\CodeConversationBundle\Entity\Project
     */
    public function findProjectBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->entityRepository->findBy($criteria, $orderBy, $limit, $offset);
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
