<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;


class NotificationRepository extends EntityRepository
{
    public function findUserNotifications($user)
    {
        return $this->createQueryBuilder('n')->where('n.user = :user')->andWhere('n.hasSeen = 0')->setParameter(':user', $user)->getQuery()->getResult();
    }
}
