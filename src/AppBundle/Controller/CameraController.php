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
        return $this->render('pages/cameras.html.twig');
    }
}
