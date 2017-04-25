<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfileType extends BaseType
{

    private $authorizationChecker;

    public function __construct(
      $class,
      AuthorizationChecker $authorizationChecker
    ){
      parent::__construct($class);
      $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder->add('enabled', HiddenType::class);

      $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
        $utilisateur = $event->getData();
        $form = $event->getForm();

        if ((!empty($utilisateur) && count($utilisateur->getCameras())) || $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
          $form->add('ipNdd', CollectionType::class, array(
                      'label' => 'Mes noms de domaines et adresse IP',
                      'entry_type' => TextType::class,
                      'allow_add' => true,
                      'allow_delete' => true,
                      'required' => false,
                      'delete_empty' => true
          ));
        }
    });
    }

    public function getBlockPrefix()
    {
      return 'form_user_profile_edit';
    }

    public function getName()
    {
      return $this->getBlockPrefix();
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }
}
