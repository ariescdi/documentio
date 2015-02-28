<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use MediaBundle\Entity\Media;
use MediaBundle\Entity\MediaCategory;
use AppBundle\Entity\Enquiry;
use AppBundle\Form\EnquiryType;

class SiteController extends Controller
{
    /**
     * Creates a new Medium entity.
     *
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * Creates a new Medium entity.
     *
     * @Route("/config/{name}/{value}", name="config")
     */
    public function configAction($name, $value)
    {
        $this->get('craue_config')->set($name, $value);
        $referer = $this->getRequest()->headers->get('referer');

        return $this->redirect($referer);
    }

    /**
     * @Route("/document/{slug}", name="document_show")
     * @Template("AppBundle:Media:show.html.twig")
     */
    public function documentShowAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:Media')->findOneBySlug($slug);

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
            10/*limit per page*/
        );

        return array('entities' => $entities);
    }

    /**
     * get last 2 medium.
     *
     * @Route("/getLast/", name="getlast")
     *
     * @Method("GET")
     * @Template("AppBundle:Media:getlast.html.twig")
     */
    public function getLastAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:Media')->findOneBy(array('isPublished' => 1), array('creationDate' => 'desc'), 1, 0);
        $result = true;
        if (!$entity) {
            // throw $this->createNotFoundException('Impossible de trouver ce document.');
            $result = 'Aucun document n\'a encore été publié';
        }

        return array(
            'entity' => $entity,
            'result' => $result,
        );
    }

    /**
     * get last 2 medium.
     *
     * @Route("/getbestmark/{count}", name="getbestmark")
     *
     * @Method("GET")
     * @Template("AppBundle:Media:getbestmark.html.twig")
     */
    public function getBestMarkAction($count)
    {
        $entities = $this->getDoctrine()->getManager()
                        ->getRepository('MediaBundle:Media')
                        ->findTop($count);

        $result = true;
        if (!$entities) {
            $result = 'Aucun document n\'a encore été publié';
        }

        return array(
            'entities' => $entities,
            'result' => $result,
        );
    }

    /**
     * @Route("/clock", name="clock")
     *
     * @Method("GET")
     * @Template("AppBundle:Media:clock.html.twig")
     */
    public function clockAction()
    {
        return [];
    }

    /**
     * get last 2 medium.
     *
     * @Route("/gettag/", name="gettag")
     *
     * @Method("GET")
     * @Template("AppBundle:Media:gettag.html.twig")
     */
    public function getTagAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:MediaKeyword')->findBy(array(), array(), 10, 0);

        $result = true;
        if (!$entities) {
            $result = 'Aucun tag n\'a encore été ajouté';
        }

        return array(
            'entities' => $entities,
            'result' => $result,
        );
    }

    /**
     * Search for a media.
     *
     * @Route("/search", name="media_search")
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $mediaRepo = $em->getRepository('MediaBundle:Media');

        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('media_search'))
                ->setMethod('POST')
                ->add('search', 'search', array(
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Recherche',
                    ), )
                )
                ->add('submit', 'submit', array(
                    'label' => ' ',
                    'attr' => array(
                        'class' => 'glyphicon glyphicon-search btn btn-primary',
                    ),
                ))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $keywords = $form->get('search')->getData();
            $keywords = explode(" ", $keywords);
            $result = $mediaRepo->findByKeywords($keywords);

            $paginator  = $this->get('knp_paginator');
            $result = $paginator->paginate(
                $result,
                $this->get('request')->query->get('page', 1)/*page number*/,
                10/*limit per page*/
            );

            return $this->render('AppBundle:Media:search_result.html.twig', array('entities' => $result));
        }

        return $this->render('AppBundle:Media:search_form.html.twig', array(
            'form'      => $form->createView(),
        ));
    }

    /**
     * Search for a media.
     *
     * @Route("/mainSearch", name="media_main_search")
     */
    public function mainSearchAction(Request $request)
    {
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('media_search'))
                ->setMethod('POST')
                ->add('search', 'search', array(
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Recherche',
                    ), )
                )
                ->add('submit', 'submit', array(
                    'label' => ' ',
                    'attr' => array(
                        'class' => 'glyphicon glyphicon-search',
                    ),
                ))
                ->getForm();

        return $this->render('AppBundle:Media:search_form.html.twig', array(
            'form'      => $form->createView(),
        ));
    }

    /**
     * Lists all MediaCategory entities.
     *
     * @Route("/liste-category", name="best_category")
     *
     * @Method("GET")
     * @Template("AppBundle:Media:getBestCategory.html.twig")
     */
    public function bestCategoryAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:MediaCategory')->findbestCategory(8);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all MediaCategory entities.
     *
     * @Route("/liste-category-footer", name="best_category_footer")
     *
     * @Method("GET")
     * @Template("AppBundle:Media:getBestFooterCategory.html.twig")
     */
    public function bestFooterCategoryAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:MediaCategory')->findbestCategory(8);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * @Route("/categorie/{slug}", name="list_category")
     * @Template("AppBundle:Media:list_category.html.twig")
     */
    public function mediaByCategoryAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:Media')->mediaByCategory($slug);

        $category = $em->getRepository('MediaBundle:MediaCategory')->findOneBySlug($slug);

        if (!$entities) {
            throw $this->createNotFoundException('Impossible de trouver des medias pour cette catégorie.');
        }

        $paginator  = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return array('entities' => $entities,'category' => $category);
    }

    /**
     * @Route("/contact", name="contact")
     * @Template("AppBundle:Site:contact.html.twig")
     */
    public function contactAction()
    {
        $enquiry = new Enquiry();
        $form = $this->createForm(new EnquiryType(), $enquiry);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact enquiry from symblog')
                    ->setFrom('enquiries@symblog.co.uk')
                    ->setTo('email@email.com')
                    ->setBody($this->renderView('AppBundle:Site:contactEmail.txt.twig', array('enquiry' => $enquiry)));
                $this->get('mailer')->send($message);

                // $this->get('session')->setFlash('blogger-notice', 'Your contact enquiry was successfully sent. Thank you!');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('contact'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }
}
