<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CameraController extends Controller
{
    /**
     * @Route("/cameras", name="cameras")
     */
    public function indexAction(Request $request)
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
        dump($cameras);
        return $this->render('pages/cameras.html.twig');
    }
}
