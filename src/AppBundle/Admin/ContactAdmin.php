<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Repository\apiKeyRepository;

class ContactAdmin extends Admin
{

    /**
    * Configuration des champs à afficher dans les actions Edit et Create Contact de Sonata
    *
    */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('nom', 'text', array('help'=> 'Nom du contact.'))
        ->add('date', 'date', array('help' => 'Date du message.'))
        ->add('email', 'email', array('help' => 'Email du contact.'))
        ->add('message', 'textarea', array('help' => 'Message du contact.'))
        ;
    }
    /**
    * Configuration des filtres utilisés pour filtrer et afficher la liste des contact
    *
    */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        ->add('nom')
        ->add('email')
        ->add('date')
        ;
    }
    /**
    * Configuration de l'affichage d'une liste de contacts
    * Spécification du champ affiché quand tous les modèles sont affichés (addIdentifier() méthode)
    *
    */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
        ->addIdentifier('nom')
        ->addIdentifier('email')
        ->addIdentifier('date')
        ;
    }
}
