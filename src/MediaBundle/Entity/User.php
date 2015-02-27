<?php

namespace MediaBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="dio_user")
 * @ORM\Entity(repositoryClass="UserRepository")
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
     * @var integer
     *
     * @ORM\Column(name="nb_connection", type="integer")
     */
    private $nbConnection = 0;

    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="owner")
     *
     * @var Media[]|\Doctrine\Common\Collections\Collection
     **/
    private $medias;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Notification", mappedBy="user")
     */
    protected $notifications;

    public function __construct()
    {
        parent::__construct();

        $this->medias = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    /**
     * Add a media.
     *
     * @param Media $media
     *
     * @return User Allow chained call.
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
     * return Media[]|ArrayCollection.
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Remove medias.
     *
     * @param \MediaBundle\Entity\Media $medias
     */
    public function removeMedia(\MediaBundle\Entity\Media $medias)
    {
        $this->medias->removeElement($medias);
    }

    /**
     * Add notifications.
     *
     * @param \AppBundle\Entity\Notification $notifications
     *
     * @return User
     */
    public function addNotification(\AppBundle\Entity\Notification $notifications)
    {
        $this->notifications[] = $notifications;

        return $this;
    }

    /**
     * Remove notifications.
     *
     * @param \AppBundle\Entity\Notification $notifications
     */
    public function removeNotification(\AppBundle\Entity\Notification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Set nbConnection.
     *
     * @param integer $nbConnection
     *
     * @return User
     */
    public function setNbConnection($nbConnection)
    {
        $this->nbConnection = $nbConnection;

        return $this;
    }

    /**
     * Get nbConnection.
     *
     * @return integer
     */
    public function getNbConnection()
    {
        return $this->nbConnection;
    }
}
