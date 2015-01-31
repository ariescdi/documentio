<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MediaType
 *
 * @ORM\Table(name="dio_mediacategory")
 * @ORM\Entity
 */
class MediaCategory
{
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
     * @ORM\Column(name="name", type="string", length=128, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=512)
     */
    private $comment;

    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="category")
     * @var Media[]|\Doctrine\Common\Collections\Collection
     **/
    private $medias;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name.
     * @return string The name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get comment.
     * @return string The comment.
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Get medias.
     * @return Media[]|\Doctrine\Common\Collections\Collection Medias list.
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * Set name.
     * @param string $name The name.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set comment.
     * @param string $comment The comment.
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Add a media.
     * @param Media $media The media to add.
     */
    public function addMedia(Media $media)
    {
        $media->setCategory($this);

        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Remove media
     *
     * @param Media $media
     */
    public function removeMedia(Media $media)
    {
        $this->medias->removeElement($media);
    }
}
