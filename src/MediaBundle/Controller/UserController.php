<?php

namespace MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MediaBundle\Entity\User;
use MediaBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="user")
     *
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:User')->findAll();
        $count = count($entities);

        return array(
            'entities' => $entities,
            'count'    => $count
        );
    }
    /**
     * Creates a new User entity.
     *
     * @Route("/", name="user_create")
     *
     * @Method("POST")
     * @Template("MediaBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     *
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     *
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $docs = $em->getRepository('MediaBundle:Media')->findByConnectedUser($id);

        $paginator  = $this->get('knp_paginator');
        $docs = $paginator->paginate(
            $docs,
            $this->get('request')->query->get('page', 1), // page number
            6 // limit per page
        );

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'docs' => $docs,
            'isAdmin' => $this->isAdmin($entity->getRoles()),
        );
    }

    /**
     * Finds and displays a UserCard.
     *
     * @Route("/user-card/{id}", name="user_card")
     *
     * @Method("GET")
     * @Template("MediaBundle:User:user-card.html.twig")
     */
    public function userCardAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $userCard = $em->getRepository('MediaBundle:User')->findOneById($id);

        if (!$userCard) {
            throw $this->createNotFoundException('Impossible de trouver cet utilisateur.');
        }


        $heureLastLogin = $userCard->getLastLogin();
        // $heureLastLogin = $heureLastLogin->getTimestamp();


        $heureActuelle = new \DateTime();
        // $heureActuelle = $heureActuelle->getTimestamp();

        $interval = $heureLastLogin->diff($heureActuelle);

        // var_dump($interval);
        // exit;

        return array('userCard' => $userCard,
                    'interval' => $interval);
    }

    public function isAdmin($roles)
    {
        return array($roles, "ROLE_ADMIN");
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     *
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
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
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="user_update")
     *
     * @Method("PUT")
     * @Template("MediaBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a User entity.
     *
     * @Route("/delete/{id}", name="user_delete")
     *
     * @Method({"DELETE","GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MediaBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver cet utilisateur.');
        }

        $em->remove($entity);
        $em->flush();

        //$this->addNotification($entity, 'delete', 'supprimé');

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
