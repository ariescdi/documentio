<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MediaTypeRepository extends EntityRepository
{

    public function nbMediaType()
    {
        $dql = "SELECT count(t) as nbMediaType FROM MediaBundle:MediaType t ";
        $q = $this->getEntityManager()->createQuery($dql);

        return $q->getSingleResult();
    }
}
