<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Opensoft\Bundle\CodeConversationBundle\Model\BranchManager as BaseBranchManager;
use Opensoft\Bundle\CodeConversationBundle\Model\BranchInterface;
use Symfony\Component\Validator\Constraint;
use Doctrine\ORM\EntityManager;

/**
 * Branch manager
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class BranchManager extends BaseBranchManager
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
     * @param \Opensoft\Bundle\CodeConversationBundle\SourceCode\RepositoryInterface $sourceCodeRepo
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }


    /**
     * @param array $criteria
     * @param array $order
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\BranchInterface[]
     */
    public function findBranchesBy(array $criteria, $order = null)
    {
        return $this->repository->findBy($criteria, $order);
    }


    public function findBranchByProjectNameAndRemoteNameAndBranchName($projectName, $remoteName, $branchName)
    {
        return $this->repository->createQueryBuilder('b')
                ->join('b.remote', 'r')
                ->join('r.project', 'p')
                ->where('r.name = :remoteName')
                ->andWhere('p.name = :projectName')
                ->andWhere('b.name = :branchName')
                ->setParameter('branchName', $branchName)
                ->setParameter('remoteName', $remoteName)
                ->setParameter('projectName', $projectName)
                ->getQuery()
                ->getOneOrNullResult();
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\BranchInterface[]
     */
    public function findBranches()
    {
        return $this->repository->findAll();
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\BranchInterface $branch
     */
    public function deleteBranch(BranchInterface $branch)
    {
        $this->em->remove($branch);
        $this->em->flush();
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\BranchInterface $branch
     * @param Boolean $andFlush
     */
    public function updateBranch(BranchInterface $branch, $andFlush = true)
    {
        $this->em->persist($branch);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

}