<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MediaController extends Controller
{
    /**
     * @Route("/medias", name="medias")
     */
    public function indexAction()
    {
        return $this->render('pages/medias.html.twig');
    }

    /**
     * @Route("/populaires", name="medias_populaires")
     */
    public function populairesAction()
    {
        $medias = $this->getDoctrine()->getRepository('AppBundle:Media')->findBy(array(), array('vote' => 'DESC'));
        return $this->render('pages/medias.html.twig', array('medias' => $medias));
    }

    /**
     * @Route("/derniers", name="medias_derniers")
     */
    public function derniersAction()
    {
        $medias = $this->getDoctrine()->getRepository('AppBundle:Media')->findBy(array(), array('vote' => 'DESC')); //date
        return $this->render('pages/medias.html.twig', array('medias' => $medias));
    }

    /**
     * @Route("/medias/{id}", name="media")
     * @ParamConverter("media", class="AppBundle:Media")
     */
    public function mediaAction(Media $media)
    {
        return $this->render('pages/media.html.twig', array('media' => $media));
    }
}
