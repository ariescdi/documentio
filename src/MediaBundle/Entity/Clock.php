<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clock
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MediaBundle\Entity\ClockRepository")
 */
class Clock
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
     * @var integer
     *
     * @ORM\Column(name="lagMinutes", type="integer")
     */
    private $lagMinutes;


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
     * Set lagMinutes
     *
     * @param integer $lagMinutes
     * @return Clock
     */
    public function setLagMinutes($lagMinutes)
    {
        $this->lagMinutes = $lagMinutes;

        return $this;
    }

    /**
     * Get lagMinutes
     *
     * @return integer 
     */
    public function getLagMinutes()
    {
        return $this->lagMinutes;
    }
}
