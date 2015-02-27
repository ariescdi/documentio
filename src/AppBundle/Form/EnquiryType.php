<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EnquiryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => "Votre nom (requis) :"));
        $builder->add('email', 'email', array('label' => "Votre adresse mail (requis) :"));
        $builder->add('subject', 'text', array('label' => "Votre objet (requis) :"));
        $builder->add('body', 'textarea', array('label' => "Votre message (requis) :"));
        $builder->add('submit', 'submit');
    }

    public function getName()
    {
        return 'contact';
    }
}
