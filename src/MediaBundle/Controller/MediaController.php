<?php

namespace MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Stof\DoctrineExtensionsBundle\Uploadable\UploadableManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use MediaBundle\Entity\Media;
use MediaBundle\Entity\MediaKeyword;
use MediaBundle\Form\MediaType;

/**
 * Media controller.
 *
 * @Route("/media")
 */
class MediaController extends Controller
{

    /**
     * Get the media itself
     * @Route("/{id}/get", name="media_get")
     */
    public function get($id)
    {
        /* @var $fs \Gaufrette\Filesystem */
        $fs = $this->container->get('fs.dio');
        
        /* @var $entity Media */
        $entity = $this->getDoctrine()->getManager()->getRepository('MediaBundle:Media')->find($id);                
        
        // create file response
        // note : not using BinaryFileResponse to allow remote file hosting managed by gaufrette
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($fs->get($entity->getPath())->getContent());
        $response->headers->set('Content-Type', $entity->getType()->getMimetype());
        $disp = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $entity->getName() . '.' . $entity->getType()->getExt()
        );
        $response->headers->set('Content-Disposition', $disp);
        return $response;
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
            throw $this->createNotFoundException('Unable to find Media entity.');
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
     * Update media keywords
     * @param string $keywords splitted by spaces
     * @param Media $entity
     */
    function updateKeywords( $keywords, Media $entity )
    {
        $em = $this->getDoctrine()->getManager();

        $kw_repo = $em->getRepository('MediaBundle:MediaKeyword');

        $keywords = array_merge($keywords, explode(' ',preg_replace('/[^\s\p{L}]/u', ' ',$entity->getName())));
        $keywords = array_merge($keywords, explode(' ',preg_replace('/[^\s\p{L}]/u', ' ',$entity->getComment())));

        foreach ($keywords as $k)
        {
            $k = strtolower($k);
            if(strlen($k))
            {
                /** @var Aries\Site\MediaBundle\Entity\MediaKeyword $kw_entity */
                $kw_entity = $kw_repo->findOneBy(array(
                    'word' => $k
                ));
                if(!$kw_entity)
                {
                    $kw_entity = new MediaKeyword();
                    $kw_entity->setWord($k);
                }
                $kw_entity->addMedia($entity);
                $entity->addKeyword($kw_entity);
                $em->persist($kw_entity);
                $em->flush();
            }
        }
    }
    
    
    /**
     * Search for a media.
     *
     * @Route("/search", name="media_search")
     */
    function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $media_repo = $em->getRepository('MediaBundle:Media');

        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('media_search'))
                ->setMethod('POST')
                ->add('search','search',array(
                    'label' => false,                    
                ))
                ->getForm();
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $keywords = $form->get('search')->getData();
            $keywords = explode(" ", $keywords);
            $result = $media_repo->findByKeywords($keywords);

            return $this->render('MediaBundle:Media:index.html.twig', array('entities' => $result));
            
        }

        return $this->render('MediaBundle:Media:search_form.html.twig', array(
            'form'      => $form->createView(),
        ));
    }
    
    
    /**
     * Decreases media mark.
     *
     * @Route("/{id}/decrease_mark", name="media_decrease_mark")
     */
    function decreaseMarkAction($id)
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
    function increaseMarkAction($id)
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
     * @Method("GET")
     */
    function showCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $media_category = $em->getRepository('MediaBundle:MediaCategory')->find($id);

        return $this->render('MediaBundle:Media:index.html.twig', array('entities' => $media_category->getMedias()));
    }
    
    /**
     * Configures dropped file.
     *
     * @Route("/drop_config", name="media_drop_config")
     * @Method("POST")
     */
    public function dropConfigAction(Request $request)
    {
        $entity = new Media();
        $form = $this->createForm(new MediaType(), $entity, array(
            'action' => $this->generateUrl('media_dropped_config'),
            'method' => 'POST',
        ));
        $form->remove('path');
        $form->add('tmpfile','hidden',array('mapped'=>false));
        $form->add('submit','submit',array('label'=>'configure'));
        $form->handleRequest($request);
        if($form->isValid())
        {
            $fname = $form->get('tmpfile')->getData();
            if(!$fname)
            {
                throw $this->createNotFoundException('Temp file not in form');
            }
            /** @var $fs \Gaufrette\Filesystem */
            $fs = $this->container->get('fs.dio');
            
            $entity->setPath($fname);
            
            $em = $this->getDoctrine()->getManager();
            
            $date = new \DateTime();
            $entity->setCreationDate($date);
            $entity->setUpdateDate($date);
            $entity->setOwner($this->getUser());
            
            $em->persist($entity);
            
            $target_path = $entity->getCategory()->getName() . '/' . $entity->getId() . '_' . $fname;
            
            $entity->setPath($target_path);
            $fs->rename('tmp/'.$fname, $target_path);
            
            $em->persist($entity);
            
            
            
            $keywords = explode(' ', $form->get('keywords')->getData());
            
            $this->updateKeywords($keywords,$entity);
            

            return $this->redirect($this->generateUrl('media_show', array('id' => $entity->getId())));
        }
        return $this->render('MediaBundle:Media:create_dropped.html.twig', array(
            'forms' => array($form->createView())
        ));
    }
    
    /**
     * Handles file drop.
     *
     * @Route("/drop", name="media_drop")
     * @Method("POST")
     */
    public function dropAction(Request $request)
    {
        $forms = array();
        /** @var $f UploadedFile A file. */
        foreach ( $request->files as $f )
        {
            //var_dump($f);
            $m = new Media();
            /** @var $fs \Gaufrette\Filesystem  */
            $fs = $this->container->get('fs.dio');
            $fs->write('tmp/'.$f->getClientOriginalName(),  file_get_contents($f->getPathname()));
            
            $form = $this->createForm(new MediaType(), $m, array(
                'action' => $this->generateUrl('media_dropped_config'),
                'method' => 'POST',
            ));

            $form->remove('path');
            $form->add('tmpfile','hidden',array('mapped'=>false,'data'=>$f->getClientOriginalName()));
            $form->add('submit','submit',array('label'=>'configure'));
            $forms[] = $form->createView();
        }
        return $this->render('MediaBundle:Media:create_dropped.html.twig', array(
            'forms' => $forms
        ));
    }
    
    /**
     * Lists all Media entities.
     *
     * @Route("/", name="media")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MediaBundle:Media')->findAll();

        return $this->render('MediaBundle:Media:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Media entity.
     *
     * @Route("/", name="media_create")
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
            
            /** @var $fs \Gaufrette\Filesystem */
            $fs = $this->container->get('fs.dio');
            
            $f = $entity->getPath();
            $fname = $f->getClientOriginalName();
            $entity->setPath($fname);
            
            $date = new \DateTime();
            $entity->setCreationDate($date);
            $entity->setUpdateDate($date);
            $entity->setOwner($this->getUser());
            
            $em->persist($entity);
            
            $target_path = $entity->getCategory()->getName() . '/' . $entity->getId() . '_' . $fname;
            $entity->setPath($target_path);
            $fs->write( $target_path,  file_get_contents($f->getPathname()));
            
            $keywords = explode(' ', $form->get('keywords')->getData());
            
            $this->updateKeywords($keywords,$entity);

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

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Media entity.
     *
     * @Route("/new", name="media_new")
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
     * @Route("/{id}", name="media_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MediaBundle:Media:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Media entity.
     *
     * @Route("/{id}/edit", name="media_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MediaBundle:Media:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
        
        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Edits an existing Media entity.
     *
     * @Route("/{id}", name="media_update")
     * @Method("PUT")
     * @Template("MediaBundle:Media:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var $entity Aries\Site\MediaBundle\Entity\Media */
        $entity = $em->getRepository('MediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }
        
        $original_path = $entity->getAbsolutePath();

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setUpdateDate(new \DateTime());
            
            if($entity->getFile())
            {
                $entity->upload();
            }
            else if($original_path != $entity->getAbsolutePath())
            {
                /** @var $file \Symfony\Component\HttpFoundation\File\File */
                $file = new \Symfony\Component\HttpFoundation\File\File($original_path);
                if($file == null)
                {
                    throw $this->createNotFoundException();
                }
                $file->move($entity->getUploadRootDir());
            }
            
            $keywords = explode(' ', $editForm->get('keywords')->getData());
            
            $this->updateKeywords($keywords,$entity);
            
            $em->flush();

            return $this->redirect($this->generateUrl('media_edit', array('id' => $id)));
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
     * @Route("/{id}", name="media_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MediaBundle:Media')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Media entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

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
                                                'class'   => 'btn btn-default'),
                ))
            ->getForm()
        ;
    }
}
