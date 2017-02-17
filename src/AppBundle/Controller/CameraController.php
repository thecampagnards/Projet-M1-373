<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Camera;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CameraController extends Controller
{
    /**
     * @Route("/cameras", name="cameras")
     */
    public function indexAction()
    {
      if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
        $cameras = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:Camera')->findAll();
      }else{
        $cameras = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:Camera')->findCameraByUtilisateur($this->getUser());
      }
        return $this->render('pages/cameras.html.twig', array('cameras' => $cameras));
    }

    /**
 * @Route("/camera/{id}", name="camera")
 * @ParamConverter("camera", class="AppBundle:Camera")
 */
    public function cameraAction(Camera $camera)
    {
        return $this->render('pages/camera.html.twig', array('camera' => $camera));
    }
}
