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
            $default_keywords = implode(' ', $tmp);
            $file_required = false;
        } else {
            $default_keywords = "";
            $file_required = true;
        }

        $builder
            ->add('name')
            ->add('comment', 'textarea')
            ->add('file', 'file', array('label' => 'File',
                    'required' => $file_required,
                ))
            //->add('creation_date')
            //->add('update_date')
            ->add('category')
            ->add('type')
            ->add('keywords', 'text', array(
                'data' => $default_keywords,
                'required' => false,
                'mapped' => false,
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
