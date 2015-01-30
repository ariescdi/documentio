<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    /**
     * @Route("/app/example", name="homepage")
     */
    public function indexAction()
    {
        $form = $this->createFormBuilder()
                ->add('text', 'text')
                ->add('submit', 'submit')
                ->getForm();

        return $this->render('default/index.html.twig', array(
                    'form' => $form->createView(),
        ));
    }
}
