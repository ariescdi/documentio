<?php

namespace MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text',array('label'=>'Nom'))
            ->add('ext','text',array('label'=>'Extention'))
            ->add('comment','text',array('label'=>'Commentaire'))
            ->add('mimetype','text',array('label'=>'Mimetype'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MediaBundle\Entity\MediaType',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mediabundle_mediatype';
    }
}
