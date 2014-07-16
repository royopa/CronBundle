<?php

namespace SymfonyContrib\Bundle\CronBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Doctrine repository for the cron entity.
 */
class CronRepository extends EntityRepository
{
    /**
     * Find all due cron jobs.
     *
     * @return array
     */
    public function findDue()
    {
        $dql = "SELECT c
                FROM CronBundle:Cron c
                WHERE c.nextRun <= :time
                    AND c.enabled = 1";

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('time', new \DateTime())
            ->getResult();
    }

    /**
     * Find all enabled cron jobs.
     *
     * @return array
     */
    public function findAllEnabled()
    {
        return $this->findBy(['enabled' => true]);
    }
}
