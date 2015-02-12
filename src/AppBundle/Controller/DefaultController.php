<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
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
     * Creates a new Medium entity.
     * @Route("/document/{id}", name="document")
     * @Template("AppBundle::media.html.twig")
     */
    public function mediaAction($id)
    {
        return [];
    }
    /**
     * get last 2 medium
     *
     * @Route("/getLast/{count}", name="getlast")
     * @Method("GET")
     * @Template("AppBundle::getlast.html.twig")
     */
    public function getLastAction($count)
    {
        $datas = array();

        return array(
            'datas' => $datas,
        );
    }
    
    /**
     * get last 2 medium
     *
     * @Route("/getbestmark/{count}", name="getbestmark")
     * @Method("GET")
     * @Template("AppBundle::getbestmark.html.twig")
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
