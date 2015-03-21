<?php

namespace MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MediaBundle\Entity\MediaType;
use MediaBundle\Form\MediaTypeType;

/**
 * MediaType controller.
 *
 * @Route("/mediatype")
 */
class MediaTypeController extends Controller
{
    /**
     * Lists all MediaType entities.
     *
     * @Route("/", name="mediatype")
     *
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:MediaType')->findAll();
        $count = count($entities);

        return array(
            'entities' => $entities,
            'count'    => $count
        );
    }
    /**
     * Creates a new MediaType entity.
     *
     * @Route("/", name="mediatype_create")
     *
     * @Method("POST")
     * @Template("MediaBundle:MediaType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new MediaType();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mediatype_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a MediaType entity.
     *
     * @param MediaType $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MediaType $entity)
    {
        $form = $this->createForm(new MediaTypeType(), $entity, array(
            'action' => $this->generateUrl('mediatype_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Créer', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }

    /**
     * Displays a form to create a new MediaType entity.
     *
     * @Route("/new", name="mediatype_new")
     *
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MediaType();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a MediaType entity.
     *
     * @Route("/{id}", name="mediatype_show")
     *
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:MediaType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MediaType entity.
     *
     * @Route("/{id}/edit", name="mediatype_edit")
     *
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:MediaType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaType entity.');
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
     * Creates a form to edit a MediaType entity.
     *
     * @param MediaType $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MediaType $entity)
    {
        $form = $this->createForm(new MediaTypeType(), $entity, array(
            'action' => $this->generateUrl('mediatype_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Mettre à jour', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }
    /**
     * Edits an existing MediaType entity.
     *
     * @Route("/{id}", name="mediatype_update")
     *
     * @Method("PUT")
     * @Template("MediaBundle:MediaType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:MediaType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('mediatype_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a MediaType entity.
     *
     * @Route("/delete/{id}", name="mediatype_delete")
     *
     * @Method({"DELETE","GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MediaBundle:MediaType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MediaType entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('mediatype'));
    }

    /**
     * Creates a form to delete a MediaType entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mediatype_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
