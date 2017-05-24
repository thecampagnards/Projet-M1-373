<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

class CameraAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {

        $camera = $this->getSubject();
        $fileFieldOptions = array('required' => false, 'help'=>'Indiquer l\'image de la caméra.', 'label' => 'Image');
        if (!empty($camera->getMedia()) && $webPath = $camera->getWebPath()) {
          if($camera->isImage()){
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request_stack')->getCurrentRequest()->getBasePath().'/'.$webPath;
            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" height="300" />';
          }
        }

        $formMapper
        ->add('nom', 'text', array('help' => 'Veuillez indiquer le nom de la caméra.'))
        ->add('viewer', 'text', array('label' => 'Spectateurs', 'help' => 'Les spectateurs du stream.', 'disabled' => true, 'required' => false))
        ->add('file', 'file', $fileFieldOptions)
        ->add('description', 'textarea', array('help' => 'Une description de la caméra.', 'required' => false))
        ->add('ip', 'text', array('help' => 'L\'adresse ip de la caméra pour récupérer les médias.', 'required' => false))
        ->add('email', 'email', array('help' => 'L\'adresse mail de la caméra pour récupérer les médias.', 'required' => false))
        ->add('emailPassword', 'password', array('help' => 'Mot de passe de l\'adresse mail de la caméra pour récupérer les médias.', 'required' => false))
        ->add('etat', 'choice', array('help'=>'Activer si vous voulez rendre la caméra public ou privée.', 'choices' => array(
          'Privé' => '0',
          'Public' => '1'
        )))
        ->add('utilisateurs', 'sonata_type_model', array(
          'required' => false,
          'by_reference' => false,
          'expanded' => true,
          'multiple' => true,
          'help' => 'Les utilisateurs pouvant accèder à la caméra en privé.'
        ), array('admin_code' => 'admin.utilisateur'))
      /*  ->add('medias', 'sonata_type_model', array(
          'required' => false,
          'by_reference' => false,
          'expanded' => true,
          'multiple' => true,
          'label' => 'Médias',
          'help' => 'Les médias associés à la caméra.',
          'template' => 'components/admin/field_media.html.twig'
        ), array('admin_code' => 'admin.media'))*/
        ;

        if (!empty($camera->getId())) {
          $formMapper
          ->add('FTPUser', 'text', array('label' => 'Utilisateur FTP', 'help' => 'Utilisateur FTP pour déposer les fichiers.', 'disabled' => true, 'required' => false))
          ->add('FTPPassword', 'text', array('label' => 'Mot de passe FTP', 'help' => 'Mot de passe FTP.', 'required' => false));
        }
    }

    public function validate(ErrorElement $errorElement, $object)
    {
      if(!empty($object->getId())){
        shell_exec(escapeshellcmd(
          'sudo '.$this->getConfigurationPool()->getContainer()->get('kernel')->getRootDir().'/../src/AppBundle/Scripts/ftp.sh camera'.$object->getId().' '.$object->getFTPPassword().' '.$this->getConfigurationPool()->getContainer()->get('kernel')->getRootDir().'/../web/uploads/camera/'.$object->getId().'/'. ($object->getEtat() ? 'public' : 'prive') .'/'
        ));
        $object->setFTPUser('camera'.$object->getId());
      }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('nom')
        ->add('ip')
        ->add('viewer', array(), array('label' => 'Spectateurs'))
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
        $listMapper
        ->addIdentifier('nom')
        ->add('image', 'string', array('template' => 'components/admin/list_camera.html.twig'))
        ->addIdentifier('ip')
        ->addIdentifier('viewer', array(), array('label' => 'Spectateurs'))
        ->addIdentifier('utilisateurs')
        ->add('_action', 'actions', array(
          'actions' => array(
            'edit' => array(),
            'delete' => array(),
          )
        ));
    }
}
