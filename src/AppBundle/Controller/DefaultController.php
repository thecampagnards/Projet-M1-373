<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $cameras = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Camera')->findByEtat(true);

        $medias = $this->getDoctrine()->getRepository('AppBundle:Media')->findBy(array(), array('created' => 'DESC'));

        return $this->render('pages/index.html.twig', array('cameras' => $cameras, 'medias' => $medias));
    }
}
