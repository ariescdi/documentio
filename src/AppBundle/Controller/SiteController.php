<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SiteController extends Controller
{
    /**
     * Creates a new Medium entity.
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }
    /**
     * @Route("/document/{slug}", name="document_show")
     * @Template("AppBundle:Media:show.html.twig")
     */
    public function documentShowAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:Media')->findBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver ce document.');
        }

        return array('entity' => $entity);
    }

    /**
     * @Route("/document/", name="document")
     * @Template("AppBundle:Media:list.html.twig")
     */
    public function documentListAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:Media')->findBy(array('isPublished' => 1), array());

        $paginator  = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            1/*limit per page*/
        );

        return array('entities' => $entities);
    }

    /**
     * get last 2 medium
     *
     * @Route("/getLast/{count}", name="getlast")
     * @Method("GET")
     * @Template("AppBundle:Media:getlast.html.twig")
     */
    public function getLastAction($count)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:Media')->findOneBy(array('isPublished' => 1), array('creationDate' => 'desc'), 1, 0);

        return array(
            'entity' => $entity,
        );
    }

    /**
     * get last 2 medium
     *
     * @Route("/getbestmark/{count}", name="getbestmark")
     * @Method("GET")
     * @Template("AppBundle:Media:getbestmark.html.twig")
     */
    public function getBestMarktAction($count)
    {
        $datas = $this->getDoctrine()->getManager()
                        ->getRepository('MediaBundle:Media')
                        ->findTop($count);

        return array(
            'datas' => $datas,
        );
    }

    /**
     * get last 2 medium
     *
     * @Route("/gettag/{count}", name="gettag")
     * @Method("GET")
     * @Template("AppBundle::gettag.html.twig")
     */
    public function getTagtAction($count)
    {
        $datas = array();

        return array(
            'datas' => $datas,
        );
    }
}
