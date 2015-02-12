<?php

namespace MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $options['data'];
        if ($entity) {
            $tmp = array();
            foreach ($entity->getKeywords() as $k) {
                $tmp[] = $k->getWord();
            }
            $defaultKeywords = implode(' ', $tmp);
            $fileRequired = false;
        } else {
            $defaultKeywords = "";
            $fileRequired = true;
        }

        $builder
            ->add('name', 'text', array('label'=>'Nom'))
            ->add('comment', 'textarea',array('label'=>'Description'))
            ->add('file', 'file', array('label' => 'Fichier',
                    'required' => $fileRequired,
                ))
            //->add('creation_date')
            //->add('update_date')
            ->add('category', null ,array('label'=>'Catégorie'))
            ->add('type')
            ->add('keywords', 'text', array(
                'data' => $defaultKeywords,
                'required' => false,
                'mapped' => false,
                'label'=>'Mots Clefs',
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MediaBundle\Entity\Media',
            'class' => 'btn btn-default',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mediabundle_media';
    }
}
