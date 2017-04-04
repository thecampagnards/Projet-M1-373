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
        $camera = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Camera')->findOneByEtat(true);

        $medias = $this->getDoctrine()->getRepository('AppBundle:Media')->findBy(array(), array('created' => 'DESC'));

        return $this->render('pages/index.html.twig', array('camera' => $camera, 'medias' => $medias));
    }
}
