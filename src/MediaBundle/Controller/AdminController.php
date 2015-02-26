<?php

namespace MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MediaBundle\Entity\Media;
use MediaBundle\Entity\MediaCategory;
use MediaBundle\Entity\MediaKeyword;
use MediaBundle\Entity\MediaType;
use MediaBundle\Entity\User;

class AdminController extends Controller
{

    /**
     * Lists all MediaCategory entities.
     *
     * @Route("/", name="admin_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:Media')->findAll();

        return $this->render('MediaBundle::index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Lists all MediaCategory entities.
     *
     * @Route("/menuadmin", name="menuadmin")
     * @Method("GET")
     * @Template("MediaBundle::menuadmin.html.twig")
     */
    public function menuadminAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entitiesmedia = $em->getRepository('MediaBundle:Media')->findAll();
        $entitiescategorie = $em->getRepository('MediaBundle:MediaCategory')->findAll();
        $entitieskeyword = $em->getRepository('MediaBundle:MediaKeyword')->findAll();
        $entitiestype = $em->getRepository('MediaBundle:MediaType')->findAll();
        $entitiesuser = $em->getRepository('MediaBundle:User')->findAll();

        return array(
            'entitiesmedia' => $entitiesmedia,
            'entitiescategorie' => $entitiescategorie,
            'entitieskeyword' => $entitieskeyword,
            'entitiestype' => $entitiestype,
            'entitiesuser' => $entitiesuser
            );
    }
}
