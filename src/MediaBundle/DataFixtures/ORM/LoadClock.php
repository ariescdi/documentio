<?php

namespace MediaBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use MediaBundle\Entity\Clock;

/**
 * Description of LoadClock.
 *
 */
class LoadClock extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @return User The created user.
     */
    public function createClock(ObjectManager $manager,$lagMinutes)
    {
        /* @var $clock Clock */
        $clock = new Clock();
        $clock->setLagMinutes($lagMinutes);
        $manager->persist($clock);

        return $clock;
    }

    public function load(ObjectManager $manager)
    {
        $this->createClock($manager,0);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
