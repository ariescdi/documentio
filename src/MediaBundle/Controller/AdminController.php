<?php

namespace MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends Controller
{
    /**
     * Statistics.
     *
     * @Route("/", name="admin_index")
     *
     * @Method("GET")
     */
    public function indexAction()
    {
        if (($this->get('security.context')->isGranted('ROLE_ADMIN'))) {
            $em = $this->getDoctrine()->getManager();

            $keywords = $em->getRepository('MediaBundle:MediaKeyword')->topKeyWord();
            $medias = $em->getRepository('MediaBundle:Media')->findTop(10);
            $users = $em->getRepository('MediaBundle:User')->findConnection();

            return $this->render('MediaBundle:Statistics:index.html.twig', array(
                'keywords'  => $keywords,
                'medias'    => $medias,
                'users'     => $users,
            ));
        } else {
            return $this->render('MediaBundle:Statistics:index_user.html.twig');
        }
    }
    /**
     * Lists all MediaCategory entities.
     *
     * @Route("/menuadmin", name="menuadmin")
     *
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
            'entitiesuser' => $entitiesuser,
            );
    }
}
