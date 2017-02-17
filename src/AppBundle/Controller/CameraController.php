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
        $cameras = $this->getDoctrine()->getRepository('AppBundle:Camera')->findAll();
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
