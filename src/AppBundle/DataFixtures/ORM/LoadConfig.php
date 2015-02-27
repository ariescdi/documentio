<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\HelloBundle\Entity\User;
use Craue\ConfigBundle\Entity\Setting;

class LoadConfig implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $setting = new Setting();
        $setting->setName('theme');
        $setting->setValue('blue');

        $manager->persist($setting);
        $manager->flush();
    }
}
