<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UserRepository extends EntityRepository
{
    /**
     * @param $token
     * @return User|null
     */
    public function getByToken($token)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.token = :token')
            ->setParameter('token', $token);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch(NoResultException $e) {
            return null;
        }
    }
}
