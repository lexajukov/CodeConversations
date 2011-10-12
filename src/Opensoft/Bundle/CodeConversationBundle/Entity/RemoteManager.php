<?php
/*
 *
 */

namespace Opensoft\Bundle\CodeConversationBundle\Entity;

use Opensoft\Bundle\CodeConversationBundle\Model\RemoteManager as BaseRemoteManager;
use Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface;
use Doctrine\ORM\EntityManager;

/**
 * Remote manager
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class RemoteManager extends BaseRemoteManager
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
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface[]
     */
    public function findRemotesBy(array $criteria, $order = null)
    {
        return $this->repository->findBy($criteria, $order);
    }

    public function findRemoteByProjectNameAndRemoteName($projectName, $remoteName)
    {
        return $this->repository->createQueryBuilder('r')
                ->join('r.project', 'p')
                ->where('r.name = :remoteName')
                ->andWhere('p.name = :projectName')
                ->setParameter('remoteName', $remoteName)
                ->setParameter('projectName', $projectName)
                ->getQuery()
                ->getOneOrNullResult();
    }

    /**
     * @return \Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface[]
     */
    public function findRemotes()
    {
        return $this->repository->findAll();
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface $remote
     */
    public function deleteRemote(RemoteInterface $remote)
    {
        $this->em->remove($remote);
        $this->em->flush();
    }

    /**
     * @param \Opensoft\Bundle\CodeConversationBundle\Model\RemoteInterface $remote
     * @param Boolean $andFlush
     */
    public function updateRemote(RemoteInterface $remote, $andFlush = true)
    {
        $this->em->persist($remote);
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
