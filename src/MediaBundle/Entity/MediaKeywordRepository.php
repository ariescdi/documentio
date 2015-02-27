<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MediaKeywordRepository extends EntityRepository
{
    public function topKeyWord()
    {
        $dql = '
            SELECT k.word, COUNT(m) AS compteur
            FROM MediaBundle:MediaKeyword k
            JOIN k.medias m
            GROUP BY m.id
            ORDER BY compteur DESC
        ';

        $q = $this->getEntityManager()->createQuery($dql);
        $q->setMaxResults(10);

        return $q->getResult();
    }
}
