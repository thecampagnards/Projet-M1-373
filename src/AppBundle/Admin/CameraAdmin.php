<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CameraAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('nom', 'text', array('help' => 'Veuillez indiquer le nom de la caméra.'))
        ->add('description', 'textarea', array('help' => 'Une description de la caméra.', 'required' => false))
        ->add('ip', 'text', array('help' => 'L\'adresse ip de la caméra pour récupérer les médias.', 'required' => false))
        ->add('email', 'email', array('help' => 'L\'adresse mail de la caméra pour récupérer les médias.', 'required' => false))
        ->add('etat', 'choice', array('help'=>'Activer si vous voulez rendre la caméra public ou privée.', 'choices' => array(
          'Privé' => '0',
          'Public' => '1'
        )))
        ->add('utilisateurs', 'entity', array('help' => 'Les utilisateurs pouvant accèder à la caméra en privé.', 'class' => 'AppBundle\Entity\Utilisateur', 'multiple' => true, 'required' => false))
        ->add('medias', 'entity', array('help' => 'Les médias associés à la caméra.', 'class' => 'AppBundle\Entity\Media', 'multiple' => true, 'required' => false))
        /*    ->add('medias', 'sonata_type_model_list', array(
        'btn_add'       => 'Ajouter un média',
        'btn_list'      => 'button.list',
        'btn_delete'    => false,
        'btn_catalogue' => 'SonataNewsBundle'
        ))
        */

        /*  ->add('utilisateurs', 'sonata_type_model_list', array(
        'btn_add'       => 'Ajouter un utilisateur',
        'btn_list'      => 'button.list',
        'btn_delete'    => false,
        'btn_catalogue' => 'SonataNewsBundle'
        ))*/

        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('nom')
        ->add('ip')
        ->add('viewer')
        ->add('etat',null, array(), 'choice', array(
          'choices' => array(
            'Privé' => '0',
            'Public' => '1'
          )
        ))
        ->add('utilisateurs');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('nom')
        ->addIdentifier('ip')
        ->addIdentifier('viewer')
        ->addIdentifier('utilisateurs')
        ->add('_action', 'actions', array(
          'actions' => array(
            'edit' => array(),
            'delete' => array(),
          )
        ));
    }
}
