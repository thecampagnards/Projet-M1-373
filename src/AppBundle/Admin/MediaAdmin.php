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

        $media = $this->getSubject();
        $fileFieldOptions = array('required' => false, 'help'=>'Indiquer un fichier.', 'label' => 'Fichier');
        if (!empty($media->getMedia()) && $webPath = $media->getWebPath()) {
          $container = $this->getConfigurationPool()->getContainer();
          $fullPath = $container->get('request_stack')->getCurrentRequest()->getBasePath().'/'.$webPath;
          if($media->isImage()){
            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" height="300" />';
          }elseif($media->isVideo()){
            $fileFieldOptions['help'] = '<video width="320" height="240" controls>
                       <source src="'.$fullPath.'" type="video/mp4">
                      Your browser does not support the video tag.
                      </video>';
          }
        }

        $formMapper
        ->add('nom', 'text', array('help' => 'Titre du média.'))
        ->add('description', 'textarea', array('help' => 'Description du média.', 'required' => false))
        ->add('etat', 'choice', array('help'=>'Activer si vous voulez rendre le média public ou privée.', 'choices' => array(
          'Privé' => '0',
          'Public' => '1'
        )))
        ->add('camera', 'sonata_type_model', array(
          'by_reference' => false,
          'expanded' => true,
          'label' => 'Caméra',
          'help' => 'La caméra associée au média.'
        ), array('admin_code' => 'admin.camera'))
        ->add('file', 'file', $fileFieldOptions)
        ->add('vote', 'text', array('help' => 'Les votes du média.', 'disabled' => true, 'required' => false));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        ->add('nom')
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
        $listMapper
        ->addIdentifier('nom')
        ->addIdentifier('vote')
        ->addIdentifier('etat', null, array('label' => 'Public'))
        ->addIdentifier('camera', null, array('label' => 'Caméra'))
        ->add('media', 'string', array('template' => 'components/admin/list_media.html.twig'))
        ->add('_action', 'actions', array(
          'actions' => array(
            'edit' => array(),
            'delete' => array(),
          )
        ));
    }
}
