<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class AppBundle extends Bundle
{
    public function boot()
    {
        $event = $this->container->get('event_dispatcher');

        $event->addListener(
            SitemapPopulateEvent::ON_SITEMAP_POPULATE,
            [$this, 'onSitemapPopulate']
        );
    }

    public function onSitemapPopulate(SitemapPopulateEvent $event)
    {
        $router = $this->container->get('router');
        $generator = $event->getGenerator();
        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository('MediaBundle:Media');
        $medias = $repository->createQueryBuilder('m')->select('m.slug', 'm.updateDate')->getQuery()->getResult();

        $generator->addUrl(new UrlConcrete(
            $router->generate('index', [], true)
        ), 'default');

        foreach ($medias as $media) {
            $generator->addUrl(new UrlConcrete(
                $router->generate('document_show', ['slug' => $media['slug']], true),
                $media['updateDate']
            ), 'default');
        }
    }
}
