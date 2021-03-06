<?php

namespace MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MediaBundle\Entity\MediaCategory;
use MediaBundle\Form\MediaCategoryType;

/**
 * MediaCategory controller.
 *
 * @Route("/mediacategory")
 */
class MediaCategoryController extends Controller
{
    /**
     * Lists all MediaCategory entities.
     *
     * @Route("/", name="mediacategory")
     *
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:MediaCategory')->findAll();
        $count = count($entities);

        return array(
            'entities' => $entities,
            'count'    => $count
        );
    }
    /**
     * Creates a new MediaCategory entity.
     *
     * @Route("/", name="mediacategory_create")
     *
     * @Method("POST")
     * @Template("MediaBundle:MediaCategory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new MediaCategory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mediacategory_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a MediaCategory entity.
     *
     * @param MediaCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MediaCategory $entity)
    {
        $form = $this->createForm(new MediaCategoryType(), $entity, array(
            'action' => $this->generateUrl('mediacategory_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Créer', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }

    /**
     * Displays a form to create a new MediaCategory entity.
     *
     * @Route("/new", name="mediacategory_new")
     *
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MediaCategory();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a MediaCategory entity.
     *
     * @Route("/{id}", name="mediacategory_show")
     *
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:MediaCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MediaCategory entity.
     *
     * @Route("/{id}/edit", name="mediacategory_edit")
     *
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:MediaCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaCategory entity.');
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
     * Creates a form to edit a MediaCategory entity.
     *
     * @param MediaCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MediaCategory $entity)
    {
        $form = $this->createForm(new MediaCategoryType(), $entity, array(
            'action' => $this->generateUrl('mediacategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Mettre à jour', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }
    /**
     * Edits an existing MediaCategory entity.
     *
     * @Route("/{id}", name="mediacategory_update")
     *
     * @Method("PUT")
     * @Template("MediaBundle:MediaCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:MediaCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('mediacategory_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a MediaCategory entity.
     *
     * @Route("/delete/{id}", name="mediacategory_delete")
     *
     * @Method({"DELETE","GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MediaBundle:MediaCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaCategory entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('mediacategory'));
    }

    /**
     * Creates a form to delete a MediaCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mediacategory_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
