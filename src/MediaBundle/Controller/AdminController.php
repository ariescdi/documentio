<?php

namespace MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MediaBundle\Entity\Media;


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
}
