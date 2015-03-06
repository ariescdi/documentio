<?php

namespace MediaBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MediaRepositoryTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testMediaBySlugFiletype()
    {
        $medias = $this->em
            ->getRepository('MediaBundle:Media')
            ->mediaBySlugFiletype(
                    array('doc'),
                    array(2)
            );

        foreach ($medias as $m) {
            echo $m->getName()."\n";
        }

        //$this->assertCount(1, $medias);
    }
}
