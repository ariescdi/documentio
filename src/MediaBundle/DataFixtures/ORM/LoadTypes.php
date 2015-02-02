<?php

namespace MediaBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use MediaBundle\Entity\MediaType;

/**
 * Description of LoadCategores
 *
 * @author Sylvain Garcia
 */
class LoadTypes extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @return MediaType The created type.
     */
    public function createType(ObjectManager $manager, $name, $comment, $ext, $mimetype)
    {
        /* @var $type MediaType */
        $type = new MediaType();

        $type->setName($name);
        $type->setComment($comment);
        $type->setExt($ext);
        $type->setMimetype($mimetype);

        $manager->persist($type);

        return $type;
    }

    public function load(ObjectManager $manager)
    {
        $this->createType($manager, 'Text', 'Txt file', 'txt', 'text/plain');
        $this->createType($manager, 'PDF', 'PDF file', 'pdf', 'application/pdf');
        $this->createType($manager, 'MarkDown', 'MD file', 'md', 'text/x-web-markdown');

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
