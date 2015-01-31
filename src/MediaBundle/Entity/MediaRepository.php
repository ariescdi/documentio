<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Query\ResultSetMapping;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Entity;

/**
 * Description of MediaRepository
 *
 * @author sylvain
 *
 * Please ensure repository is registered :
 * 
services:    
    media.repository:
        class: Site\Aries\MediaBundle\Entity\MediaRepository
        factory_service: doctrine.orm.default_entity_manager
        factory_method:  getRepository
        arguments:
            - Site\Aries\MediaBundle\Entity\Media
 * 
 */
class MediaRepository extends EntityRepository
{
    /**
     * Find media by given keywords.
     * @param string[] $keywords
     * @return Media[] Matched medias.
     */
    /*

see : http://stackoverflow.com/questions/4763143/sql-for-applying-conditions-to-multiple-rows-in-a-join

I°) Using joins

SELECT m.* FROM dio_media AS m

JOIN dio_mediakeywords_media AS km
ON m.id = km.media_id
JOIN dio_mediakeyword AS k 
ON k.id = km.keyword_id 
AND k.word = 'test'

JOIN dio_mediakeywords_media AS km2
ON m.id = km2.media_id
JOIN dio_mediakeyword AS k2
ON k2.id = km2.keyword_id 
AND k2.word = 'toto'

II°) Using INTERSECT

SELECT m.* FROM dio_media AS m

WHERE m.id IN (
	SELECT km.media_id FROM dio_mediakeywords_media as km
	JOIN dio_mediakeyword AS k ON k.id = km.keyword_id 
	AND k.word = 'test'
	INTERSECT
	SELECT km.media_id FROM dio_mediakeywords_media as km
	JOIN dio_mediakeyword AS k ON k.id = km.keyword_id 
	AND k.word = 'toto'
)

     */
    public function findByKeywords($keywords)
    {
        if (!count($keywords))
        {
            return null;
        }
        
        $sql = "SELECT m.id FROM dio_media AS m
                WHERE m.id IN (
                        SELECT km.media_id FROM dio_mediakeywords_media as km
                        JOIN dio_mediakeyword AS k ON k.id = km.keyword_id 
                        AND k.word = ?";
        
        for ( $ii = 1; $ii < count($keywords); ++$ii )
        {
            
            $sql = $sql .   "
                        INTERSECT
                        SELECT km.media_id FROM dio_mediakeywords_media AS km
                        JOIN dio_mediakeyword AS k ON k.id = km.keyword_id 
                        AND k.word = ?";
        }
        
        $sql = $sql . ")";
        
        //$sql = "SELECT m.*, o.id AS owner_id FROM dio_media AS m WHERE m.id IN ( " . $sql . ")";
        /*$sql = "SELECT m, o FROM dio_media AS m "
                . "JOIN dio_user o ON m.user_id = o.id "
                . "WHERE m.id IN ( " . $sql . ")";
        */
                
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Aries\Site\MediaBundle\Entity\Media', 'm');
        $rsm->addFieldResult('m', 'id', 'id');
        
        $q = $this->getEntityManager()->createNativeQuery($sql, $rsm);
                
        for ( $ii = 0; $ii < count($keywords); ++$ii )
        {
            $q->setParameter($ii+1, strtolower($keywords[$ii]));
        }
                
        $tmp_medias = $q->getResult();
        
        // clear entity manager for Media
        $this->getEntityManager()->clear('Aries\Site\MediaBundle\Entity\Media');
        
        $ids = array();
        
        foreach ($tmp_medias as $m)
        {
            $ids[] = $m->getId();
        }
        
        $medias = $this->findBy(array('id'=>$ids));
        /*
        echo "Found " . count($medias) . " medias :\n";
        
        foreach ($medias as $m)
        {
            echo "\tName    : " . $m->getName() . "\n";
            echo "\tComment : " . $m->getComment() . "\n";
            echo "\tPath    : " . $m->getPath() . "\n";
            echo "\t\tCategory : " . $m->getCategory()->getName() . "\n";
            echo "\t\tType     : " . $m->getType()->getName() . "\n";
            echo "\t\tOwner    : " . $m->getOwner()->getUsername() . "\n";
            echo "\t\tEmail    : " . $m->getOwner()->getEmail() . "\n";
              
        }
        /*/ //*/
        return $medias;
    }
    
    public function findLastModified($count = 5)
    {
        $dql = "SELECT m FROM MediaBundle:Media m ORDER BY m.update_date DESC";
        $q = $this->getEntityManager()->createQuery($dql);
        $q->setMaxResults($count);
        return $q->getResult();
    }
    
    public function findTop($count = 5)
    {
        $dql = "SELECT m FROM MediaBundle:Media m ORDER BY m.mark DESC";
        $q = $this->getEntityManager()->createQuery($dql);
        $q->setMaxResults($count);
        return $q->getResult();
    }
}