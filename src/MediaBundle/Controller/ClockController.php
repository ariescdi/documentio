<?php

namespace MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MediaBundle\Entity\Clock;
use MediaBundle\Form\ClockType;

/**
 * Clock controller.
 *
 * @Route("/clock")
 */
class ClockController extends Controller
{

    /**
     * Displays a form to edit an existing Clock entity.
     *
     * @Route("/edit", name="clock_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:Clock')->find(1);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Clock entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Clock entity.
    *
    * @param Clock $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Clock $entity)
    {
        $form = $this->createForm(new ClockType(), $entity, array(
            'action' => $this->generateUrl('clock_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Modifier', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }
    /**
     * Edits an existing Clock entity.
     *
     * @Route("/{id}", name="clock_update")
     * @Method("PUT")
     * @Template("MediaBundle:Clock:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:Clock')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Clock entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('clock_edit'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
}
