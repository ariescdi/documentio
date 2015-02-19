<?php

namespace MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MediaBundle\Entity\User',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mediabundle_user';
    }
}
