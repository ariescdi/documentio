<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MediaKeyWord
 *
 * @ORM\Table(name="dio_mediakeyword",indexes={@ORM\Index(name="search_idx", columns={"word"})})
 * @ORM\Entity
 */
class MediaKeyword {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="word", type="string", length=255, nullable=false)
     */
    private $word;
    
    /**
     * @var Media[]|ArrayCollection
     * 
     * @ORM\ManyToMany(targetEntity="Media",inversedBy="keywords",cascade={"remove"})
     *
     * @ORM\JoinTable(name="dio_mediakeywords_media",
     *      joinColumns={@ORM\JoinColumn(name="keyword_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $medias;
    
    public function __construct() 
    {
        $this->medias = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Add a media.
     * @param Media $media The media to add.
     * @return MediaKeyword Allows chained call.
     */
    public function addMedia(Media $media)
    {
        if (!$this->medias->contains($media))
        {
            $this->medias->add($media);
        }
        return $this;
    }
    
    /**
     * Get medias.
     * @return Media[]|ArrayCollection The media array.
     */
    public function getMedias()
    {
        return $this->medias;
    }
    
    /**
     * Set word.
     * @param string $word The word.
     * @return MediaKeyword Allows chained call.
     */
    public function setWord($word)
    {
        $this->word = $word;
    }
    
    /**
     * Get word.
     * @return string The word.
     */
    public function getWord()
    {
        return $this->word;
    }
    

    /**
     * Remove medias
     *
     * @param \Aries\Site\MediaBundle\Entity\Media $medias
     */
    public function removeMedia(\Aries\Site\MediaBundle\Entity\Media $medias)
    {
        $this->medias->removeElement($medias);
    }
    
    public function __toString() {
        return $this->word;
    }
}
