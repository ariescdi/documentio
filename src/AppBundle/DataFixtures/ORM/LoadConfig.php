<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Craue\ConfigBundle\Entity\Setting;

class LoadConfig implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $setting = new Setting();
        $setting->setName('theme');
        $setting->setValue('#23a3de');
        $manager->persist($setting);
        $manager->flush();

        $setting = new Setting();
        $setting->setName('debug');
        $setting->setValue(0);
        $manager->persist($setting);
        $manager->flush();
    }
}
