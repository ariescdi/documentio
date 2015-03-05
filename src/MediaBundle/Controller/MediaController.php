<?php

namespace MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MediaBundle\Entity\Media;
use AppBundle\Entity\Notification;
use MediaBundle\Entity\MediaKeyword;
use MediaBundle\Form\MediaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Media controller.
 *
 * @Route("/media")
 */
class MediaController extends Controller
{
    private static function getUploadRoot()
    {
        return 'uploads/dio';
    }

    /**
     * Finds and displays a Media entity.
     *
     * @Route("/{id}/display", name="media_display")
     */
    public function displayAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver ce document.');
        }

        return $this->render('MediaBundle:Media:display.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Get lattest medias.
     *
     * @Route("/last/{count}", name="media_get_last")
     */
    public function getLastModifiedAction($count)
    {
        return $this->render('MediaBundle:Media:last_modified.html.twig',
                array(
                    'medias' => $this->getDoctrine()->getManager()
                                ->getRepository('MediaBundle:Media')
                                ->findLastModified($count),
                ));
    }

    /**
     * Get best rated medias.
     *
     * @Route("/top/{count}", name="media_get_top")
     */
    public function getTopAction($count)
    {
        return $this->render('MediaBundle:Media:last_modified.html.twig',
                array(
                    'medias' => $this->getDoctrine()->getManager()
                                ->getRepository('MediaBundle:Media')
                                ->findTop($count),
                ));
    }

    /**
     * Update media keywords.
     *
     * @param string $keywords splitted by spaces (optionally double quoted)
     * @param Media  $entity
     */
    public function updateKeywords($keywords, Media $entity)
    {
        // http://stackoverflow.com/questions/2202435/php-explode-the-string-but-treat-words-in-quotes-as-a-single-word
        preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $keywords, $matches);

        $keywords = $matches[0];

        $em = $this->getDoctrine()->getManager();

        $kwRepo = $em->getRepository('MediaBundle:MediaKeyword');

        foreach ($keywords as $k) {
            $k = trim($k, '"');
            $k = strtolower($k);
            if (strlen($k)) {
                /** @var Aries\Site\MediaBundle\Entity\MediaKeyword $kwEntity */
                $kwEntity = $kwRepo->findOneBy(array(
                    'word' => $k,
                ));
                if (!$kwEntity) {
                    $kwEntity = new MediaKeyword();
                    $kwEntity->setWord($k);
                }
                $kwEntity->addMedia($entity);
                $entity->addKeyword($kwEntity);
                $em->persist($kwEntity);
                $em->flush();
            }
        }
    }

    /**
     * Decreases media mark.
     *
     * @Route("/{id}/decrease_mark", name="media_decrease_mark")
     */
    public function decreaseMarkAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $media = $em->getRepository('MediaBundle:Media')->find($id);

        $media->decrementMark();
        $em->persist($media);
        $em->flush();

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * Increases media mark.
     *
     * @Route("/{id}/increase_mark", name="media_increase_mark")
     */
    public function increaseMarkAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $media = $em->getRepository('MediaBundle:Media')->find($id);

        $media->incrementMark();
        $em->persist($media);
        $em->flush();

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * Show media for a given category.
     *
     * @Route("/show/category/{id}", name="media_show_category")
     *
     * @Method("GET")
     */
    public function showCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $mediaCategory = $em->getRepository('MediaBundle:MediaCategory')->find($id);

        return $this->render('MediaBundle:Media:index.html.twig', array('entities' => $mediaCategory->getMedias()));
    }

    /**
     * Configures dropped file.
     *
     * @Route("/drop_config", name="media_drop_config")
     *
     * @Method("POST")
     */
    public function dropConfigAction(Request $request)
    {
        $entity = new Media();
        $form = $this->createForm(new MediaType(), $entity, array(
            'action' => $this->generateUrl('media_drop_config'),
            'method' => 'POST',
        ));
        $form->remove('file');
        $form->add('tmpfile', 'hidden', array('mapped' => false));
        $form->add('submit', 'submit', array('label' => 'Créer', 'attr' => array('class' => 'btn btn-default')));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $fname = $form->get('tmpfile')->getData();
            if (!$fname) {
                throw $this->createNotFoundException('Temp file not in form');
            }

            $em = $this->getDoctrine()->getManager();

            $f = new \Symfony\Component\HttpFoundation\File\File('tmp/'.$fname);

            $entity->setPath('tmp/'.$fname);

            $entity->setOwner($this->getUser());

            $em->persist($entity);
            $em->flush();

            $f->move(MediaController::getUploadRoot().'/'.$entity->getCategory()->getName(),
                     $entity->getId().'_'.$fname);
            $entity->setPath($entity->getCategory()->getName().'/'.$entity->getId().'_'.$fname);

            $em->persist($entity);

            $this->updateKeywords($form->get('keywords')->getData(), $entity);

            $em->flush();

            return $this->redirect($this->generateUrl('media_show', array('id' => $entity->getId())));
        }

        return $this->render('MediaBundle:Media:create_dropped.html.twig', array(
            'forms' => array($form->createView()),
        ));
    }

    /**
     * Handles file drop.
     *
     * @Route("/drop", name="media_drop")
     *
     * @Method("POST")
     */
    public function dropAction(Request $request)
    {
        $datas = array();
        /* @var $f UploadedFile */
        foreach ($request->files as $f) {
            //var_dump($f);
            $m = new Media();

            // store uploaded file in tmp folder
            $f->move('tmp', $f->getClientOriginalName());

            $form = $this->createForm(new MediaType(), $m, array(
                'action' => $this->generateUrl('media_drop_config'),
                'method' => 'POST',
            ));

            $form->remove('file');
            $form->add('tmpfile', 'hidden', array('mapped' => false, 'data' => $f->getClientOriginalName()));
            $form->add('submit', 'submit', array('label' => 'Créer', 'attr' => array('class' => 'btn btn-default')));
            $data = array();
            $data['filename'] = $f->getClientOriginalName();
            $data['form'] = $form->createView();
            $datas[] = $data;
        }

        // show configuration page
        return $this->render('MediaBundle:Media:create_dropped.html.twig', array(
            'datas' => $datas,
        ));
    }

    /**
     * Lists all Media entities.
     *
     * @Route("/", name="media")
     *
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:Media')->findBy(array(), array('isPublished' => 'asc'));
        $paginator  = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('MediaBundle:Media:index.html.twig', array(
            'entities' => $entities,
            'user_id'   => $userId,
        ));
    }
    /**
     * Creates a new Media entity.
     *
     * @Route("/", name="media_create")
     *
     * @Method("POST")
     * @Template("MediaBundle:Media:new.html.twig")
     */
    public function createAction(Request $request)
    {
        /* @var $entity Media */
        $entity = new Media();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->setOwner($this->getUser());

            /* @var $f Symfony\Component\HttpFoundation\File\UploadedFile */
            $f = $entity->getFile();

            $fname = $f->getClientOriginalName();

            $entity->setPath($entity->getCategory()->getName().'/'.$fname);

            $em->persist($entity);
            $em->flush();

            $f->move(MediaController::getUploadRoot().'/'.$entity->getCategory()->getName(),
                     $entity->getId().'_'.$fname);
            $entity->setPath($entity->getCategory()->getName().'/'.$entity->getId().'_'.$fname);

            $em->persist($entity);

            $this->updateKeywords($form->get('keywords')->getData(), $entity);

            $em->flush();

            if (($this->get('security.context')->isGranted('ROLE_USER')) && (!$this->get('security.context')->isGranted('ROLE_ADMIN'))) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Un nouveau media est en cours de modération')
                    ->setFrom('site@site.fr')
                    ->setTo('email@email.com')
                    ->setBody($this->renderView('MediaBundle:Mail:moderationEmail.txt.twig'));
                $this->get('mailer')->send($message);
            }

            return $this->redirect($this->generateUrl('media_show', array('id' => $entity->getId())));
        }

        return $this->render('MediaBundle:Media:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Media entity.
     *
     * @param Media $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Media $entity)
    {
        $form = $this->createForm(new MediaType(), $entity, array(
            'action' => $this->generateUrl('media_create'),
            'method' => 'POST',
        ));

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $form->add('isPublished', null, array('label' => 'Publié', 'required'  => false));
        }

        $form->add('submit', 'submit', array('label' => 'Créer', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }

    /**
     * Displays a form to create a new Media entity.
     *
     * @Route("/new", name="media_new")
     *
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Media();
        $form   = $this->createCreateForm($entity);

        return $this->render('MediaBundle:Media:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Media entity.
     *
     * @Route("/{id}", name="media_show", options={"expose"=true})
     *
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:Media')->find($id);
        $ownerId = $entity->getOwner()->getId();

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver ce document.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MediaBundle:Media:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'user_id'      => $userId,
            'owner_id'     => $ownerId,
        ));
    }

    /**
     * Displays a form to edit an existing Media entity.
     *
     * @Route("/{id}/edit", name="media_edit")
     *
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $entity  = $em->getRepository('MediaBundle:Media')->find($id);
        $ownerId = $entity->getOwner()->getId();

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver ce document.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MediaBundle:Media:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'user_id'      => $userId,
            'owner_id'     => $ownerId,
        ));
    }

    /**
     * Creates a form to edit a Media entity.
     *
     * @param Media $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Media $entity)
    {
        $form = $this->createForm(new MediaType(), $entity, array(
            'action' => $this->generateUrl('media_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $form->add('isPublished', null, array('label' => 'Publié', 'required'  => false));
        }

        $form->add('submit', 'submit', array('label' => 'Mettre à jour', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }

    /**
     * Edits an existing Media entity.
     *
     * @Route("/{id}", name="media_update")
     *
     * @Method("PUT")
     * @Template("MediaBundle:Media:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $entity Media */
        $entity = $em->getRepository('MediaBundle:Media')->find($id);

        $entityPublished = $entity->getIsPublished();

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver ce document.');
        }

        $originalCategory = $entity->getCategory()->getId();

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $isPublished = $entity->getIsPublished();

            if ($entity->getFile()) {
                $f = $entity->getFile();

                $fname = $f->getClientOriginalName();
                $f->move(MediaController::getUploadRoot().'/'.$entity->getCategory()->getName(),
                         $entity->getId().'_'.$fname);
                $entity->setPath($entity->getCategory()->getName().'/'.$entity->getId().'_'.$fname);

                $em->persist($entity);
            } elseif ($originalCategory != $entity->getCategory()->getId()) {
                /* @var $f \Symfony\Component\HttpFoundation\File\File */
                $f = new \Symfony\Component\HttpFoundation\File\File(MediaController::getUploadRoot().'/'.$entity->getPath());
                if ($f == null) {
                    throw $this->createNotFoundException();
                }

                $fname = $f->getBasename();
                $f->move(MediaController::getUploadRoot().'/'.$entity->getCategory()->getName(),
                         $fname);
                $entity->setPath($entity->getCategory()->getName().'/'.$fname);
            }

            $this->updateKeywords($form->get('keywords')->getData(), $entity);

            $em->flush();

            if (!$entityPublished && $isPublished) {
                $this->addNotification($entity, 'pusblish', 'publié');
            } elseif ($entityPublished && !$isPublished) {
                $this->addNotification($entity, 'unpusblish', 'dépublié');
            } else {
                $this->addNotification($entity, 'edit', 'édité');
            }

            return $this->redirect($this->generateUrl('media_show', array('id' => $id)));
        }

        return $this->render('MediaBundle:Media:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Media entity.
     *
     * @Route("/delete/{id}", name="media_delete")
     *
     * @Method({"DELETE","GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver ce document.');
        }

        $em->remove($entity);
        $em->flush();

        //$this->addNotification($entity, 'delete', 'supprimé');

        return $this->redirect($this->generateUrl('media'));
    }

    /**
     * Creates a form to delete a Media entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('media_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete',
                                            'attr'   =>  array(
                                                'class'   => 'btn btn-default', ),
                ))
            ->getForm()
        ;
    }

    public function getUser()
    {
        $securityContext = $this->container->get('security.context');
        $user = $this->get('security.context')->getToken()->getUser();

        return $user;
    }

    /**
     * Add a notification.
     *
     * @param Media $entity The entity
     * @param Feedback for Notification
     * @param Status for Notification Message
     */
    public function addNotification(Media $media, $feedback, $status)
    {
        $user = $this->getUser();
        $notification = new Notification();
        $notification->setMessage('Le média '.$media->getName().' a été '.$status.' par '.$user);
        $notification->setFeedback($feedback);
        $notification->setUser($media->getOwner());
        $notification->setMedia($media);
        $notification->setHasSeen(0);

        $em = $this->getDoctrine()->getManager();
        $em->persist($notification);
        $em->flush();
    }

    /**
     * @Route("/notifications", name="notifications", options={"expose"=true})
     */
    public function getUserNotificationsAction()
    {
        //$user = $this->getUser();
        //$userId = $user->getId();
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppBundle:Notification')->findUserNotifications($this->getUser()->getId());
        $count = count($entities);
        //return json_encode($entities);
        $response = new JsonResponse();
        $response->setData($entities);

        return $response;
    }

    /**
     * @Route("/seenNotification/{id}", name="seen_notification", options={"expose"=true})
     */
    public function seenNotification($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Notification')->find($id);
        $entity->setHasSeen(1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response();
    }
}
