<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class MediaAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('nom', 'text', array('help' => 'Titre du média.'))
        ->add('description', 'textarea', array('help' => 'Description du média.', 'required' => false))
        ->add('etat', 'choice', array('help'=>'Activer si vous voulez rendre le média public ou privée.', 'choices' => array(
          'Privé' => '0',
          'Public' => '1'
        )))
        ->add('camera', 'entity', array('help' => 'La caméra du média.', 'class' => 'AppBundle\Entity\Camera'))
        ->add('fichier', 'entity', array('help' => 'Le fichier du média.', 'class' => 'AppBundle\Entity\Fichier'))
        ->add('vote', 'text', array('help' => 'Les votes du média.', 'disabled' => true, 'required' => false));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('nom')
        ->add('vote')
        ->add('etat',null, array(), 'choice', array(
          'choices' => array(
            'Privé' => '0',
            'Public' => '1'
          )
        ))
        ->add('camera');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('nom')
        ->addIdentifier('vote')
        ->addIdentifier('etat')
        ->addIdentifier('camera')
        ->add('_action', 'actions', array(
          'actions' => array(
            'edit' => array(),
            'delete' => array(),
          )
        ));
    }
}
