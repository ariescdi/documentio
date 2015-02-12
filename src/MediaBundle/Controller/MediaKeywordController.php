<?php

namespace MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MediaBundle\Entity\MediaKeyword;
use MediaBundle\Form\MediaKeywordType;

/**
 * MediaKeyword controller.
 *
 * @Route("/mediakeyword")
 */
class MediaKeywordController extends Controller
{

    /**
     * Lists all MediaKeyword entities.
     *
     * @Route("/", name="mediakeyword")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:MediaKeyword')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new MediaKeyword entity.
     *
     * @Route("/", name="mediakeyword_create")
     * @Method("POST")
     * @Template("MediaBundle:MediaKeyword:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new MediaKeyword();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mediakeyword_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a MediaKeyword entity.
     *
     * @param MediaKeyword $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MediaKeyword $entity)
    {
        $form = $this->createForm(new MediaKeywordType(), $entity, array(
            'action' => $this->generateUrl('mediakeyword_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Créer', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }

    /**
     * Displays a form to create a new MediaKeyword entity.
     *
     * @Route("/new", name="mediakeyword_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MediaKeyword();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a MediaKeyword entity.
     *
     * @Route("/{id}", name="mediakeyword_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:MediaKeyword')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaKeyword entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MediaKeyword entity.
     *
     * @Route("/{id}/edit", name="mediakeyword_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:MediaKeyword')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaKeyword entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a MediaKeyword entity.
     *
     * @param MediaKeyword $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MediaKeyword $entity)
    {
        $form = $this->createForm(new MediaKeywordType(), $entity, array(
            'action' => $this->generateUrl('mediakeyword_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Mettre à jour', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }
    /**
     * Edits an existing MediaKeyword entity.
     *
     * @Route("/{id}", name="mediakeyword_update")
     * @Method("PUT")
     * @Template("MediaBundle:MediaKeyword:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:MediaKeyword')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaKeyword entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('mediakeyword_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a MediaKeyword entity.
     *
     * @Route("/delete/{id}", name="mediakeyword_delete")
     * @Method({"DELETE","GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        // $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MediaBundle:MediaKeyword')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaKeyword entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('mediakeyword'));
    }

    /**
     * Creates a form to delete a MediaKeyword entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mediakeyword_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Supprimer', 'attr' => array('class' => 'btn btn-default')))
            ->getForm()
        ;
    }
}
