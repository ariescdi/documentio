<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Entity;


class MediaCategoryRepository extends EntityRepository
{
    public function findbestCategory($count)
    {
        $dql = "SELECT c.name, c.slug, COUNT(c) AS compteur FROM MediaBundle:MediaCategory c JOIN c.medias m GROUP BY c.id  ORDER BY compteur DESC";
        $q = $this->getEntityManager()->createQuery($dql);
        $q->setMaxResults($count);

        return $q->getResult();
    }


}
