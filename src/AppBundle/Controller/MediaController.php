<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class MediaController extends Controller
{
    /**
     * @Route("/medias", name="medias")
     */
    public function indexAction(Request $request)
    {
        return $this->render('pages/medias.html.twig');
    }

    /**
     * @Route("/populaires", name="medias_populaires")
     */
    public function populairesAction(Request $request)
    {
        return $this->render('pages/populaires.html.twig');
    }

    /**
     * @Route("/derniers", name="medias_derniers")
     */
    public function derniersAction(Request $request)
    {
        return $this->render('pages/derniers.html.twig');
    }
}
