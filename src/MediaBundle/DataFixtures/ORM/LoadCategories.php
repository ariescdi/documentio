<?php

namespace MediaBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use MediaBundle\Entity\MediaCategory;

/**
 * Description of LoadCategores
 *
 * @author Sylvain Garcia
 */
class LoadCategories extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @return MediaCategory The created category.
     */
    public function createCategory(ObjectManager $manager, $name, $comment)
    {
        /* @var $cat MediaCategory */
        $cat = new MediaCategory();

        $cat->setName($name);
        $cat->setComment($comment);

        $manager->persist($cat);

        return $cat;
    }

    public function load(ObjectManager $manager)
    {
        $this->createCategory($manager, 'Doc', 'Documentation');
        $this->createCategory($manager, 'Cheat Sheets', 'Cheat Sheets');
        $this->createCategory($manager, 'Gestion', 'Gestion');

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
