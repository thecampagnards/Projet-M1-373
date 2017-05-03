<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\CallbackTransformer;
use FOS\UserBundle\Model\UserManagerInterface;

//use AppBundle\Form\UtilisateurType;

class UtilisateurAdmin extends AbstractAdmin
{

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    public function __construct($code, $class, $baseControllerName, UserManagerInterface $userManager)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->userManager = $userManager;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {

        //$builder = $formMapper->getFormBuilder()->getFormFactory()->createBuilder(UtilisateurType::class);

        $formMapper
        ->add('username', 'text', array('help' => 'Veuillez indiquer le nom de compte.'))
        ->add('email', 'email', array('help' => 'L\'adresse mail de l\'utilisateur.'))
        ->add('enabled', 'choice', array('help'=>'Activer si vous voulez rendre la caméra public ou privée.', 'choices' => array(
          'Désactivé' => '0',
          'Activé' => '1'
        )))

        ->add('plainPassword', 'text', array(
          'label' => 'Mot de passe',
          'help' => 'Mot de passe de l\'utilisateur.',
          'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
        ))

      /*  ->add('cameras', 'sonata_type_model', array(
          'required' => false,
          'by_reference' => false,
          'expanded' => true,
          'multiple' => true,
          'label' => 'Caméras',
          'help' => 'Les caméras associés à l\'utilisateur.'
        ), array('admin_code' => 'admin.camera'))
*/
        //->add($builder->get('ipNdd'))

        /*->add('ipNdd', 'sonata_type_model', array(
          'required' => false,
          'expanded' => true,
          'multiple' => true,
          'label' => 'IP / Noms de domaine',
          'help' => 'Les ip et noms de domaine associés à l\'utilisateur.'
        ))*/
        ;
    }

    public function preUpdate($user)
    {
        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        ->add('username')
        ->add('ipNdd')
        ->add('email')
        ->add('enabled',null, array(), 'choice', array(
          'choices' => array(
            'Désactivé' => '0',
            'Activé' => '1'
          )
        ))
        ->add('lastLogin')
        ;
      }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('username')
        ->addIdentifier('email')
        ->add('enabled', null, array('editable' => true))
        ->add('lastLogin')
        ;
    }
}
