<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MediaType
 *
 * @ORM\Table(name="dio_mediatype")
 * @ORM\Entity
 */
class MediaType
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, length=128, nullable=true, name="name")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, length=32, nullable=true, name="ext")
     */
    private $ext;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, length=255, nullable=true, name="comment")
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, nullable=true, name="mimetype")
     */
    private $mimetype;

    /**
     * @ORM\OneToMany(targetEntity="MediaBundle\Entity\Media", mappedBy="type")
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
     * Get extension.
     * @return string The extension.
     */
    public function getExt()
    {
        return $this->ext;
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
     * Get the mime type.
     * @return string The code.
     */
    public function getMimetype()
    {
        return $this->mimetype;
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
     * Set extension.
     * @param string $ext The extention.
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
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
     * Set display code.
     * @param string $code The code to use to display the media.
     */
    public function setMimetype($code)
    {
        $this->mimetype = $code;
    }

    /**
     * Add a media.
     * @param Media $media The media to add.
     */
    public function addMedia(Media $media)
    {
        $media->setType($this);

        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Remove medias
     *
     * @param Media $media
     */
    public function removeMedia(Media $media)
    {
        $this->medias->removeElement($media);
    }
}
