<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ipNdd', CollectionType::class, array(
          'label' => 'IP / Noms de domaine (Pour les iFrames)',
          'entry_type'   => TextType::class,
          'allow_add' => true,
          'allow_delete' => true,
          'delete_empty' => true
        ));
    }
}
