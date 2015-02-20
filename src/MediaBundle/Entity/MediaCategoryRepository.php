<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\EntityRepository;


class MediaCategoryRepository extends EntityRepository
{
    public function findbestCategory($count)
    {
        $dql = '
            SELECT c.name, c.slug, COUNT(c) AS compteur
            FROM MediaBundle:MediaCategory c
            JOIN c.medias m
            GROUP BY c.id
            ORDER BY compteur DESC
        ';

        $q = $this->getEntityManager()->createQuery($dql);
        $q->setMaxResults($count);

        return $q->getResult();
    }
}
