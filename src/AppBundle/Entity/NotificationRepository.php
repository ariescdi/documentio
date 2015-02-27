<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class NotificationRepository extends EntityRepository
{
    public function findUserNotifications($user)
    {
        //return $this->createQueryBuilder('n')->select('n')->where('n.user = :user')->andWhere('n.hasSeen = 0')->setParameter(':user', $user)->getQuery()->getResult(); -> SHIT
        $conn = $this->getEntityManager()->getConnection();
        $users = $conn->fetchAll("SELECT id, message, media_id FROM Notification WHERE user_id = $user AND hasSeen = 0");

        return $users;
    }
}
