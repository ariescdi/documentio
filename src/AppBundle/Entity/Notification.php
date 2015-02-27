<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="NotificationRepository")
 */
class Notification
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
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="feedback", type="string", length=255, options={"default":0})
     */
    private $feedback;

    /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media", inversedBy="notifications")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    protected $media;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasSeen", type="boolean")
     */
    private $hasSeen;

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
     * Set message.
     *
     * @param string $message
     *
     * @return Notification
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set feedback.
     *
     * @param string $feedback
     *
     * @return Notification
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;

        return $this;
    }

    /**
     * Get feedback.
     *
     * @return string
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Set user.
     *
     * @param \MediaBundle\Entity\User $user
     *
     * @return Notification
     */
    public function setUser(\MediaBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \MediaBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set media.
     *
     * @param \MediaBundle\Entity\Media $media
     *
     * @return Notification
     */
    public function setMedia(\MediaBundle\Entity\Media $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media.
     *
     * @return \MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set hasSeen.
     *
     * @param boolean $hasSeen
     *
     * @return Notification
     */
    public function setHasSeen($hasSeen)
    {
        $this->hasSeen = $hasSeen;

        return $this;
    }

    /**
     * Get hasSeen.
     *
     * @return boolean
     */
    public function getHasSeen()
    {
        return $this->hasSeen;
    }
}
