<?php

namespace MediaBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="dio_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="owner")
     * @var Media[]|\Doctrine\Common\Collections\Collection
     **/
    private $medias;

    public function __construct()
    {
        parent::__construct();

        $this->medias = new ArrayCollection();
    }

    /**
     * Add a media.
     * @param  Media $media
     * @return User  Allow chained call.
     */
    public function addMedia($media)
    {
        $media->setOwner($this);
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
        }

        return $this;
    }

    /**
     * Get medias.
     * return Media[]|ArrayCollection
     */
    public function getMedias()
    {
        return $this->medias;
    }
}
