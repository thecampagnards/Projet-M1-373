<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Media;
use AppBundle\Entity\Vote;

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
        $medias = $this->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:Media')->orderByVotes();
        return $this->render('pages/medias.html.twig', array('medias' => $medias));
    }

    /**
     * @Route("/derniers", name="medias_derniers")
     */
    public function derniersAction()
    {
        $medias = $this->getDoctrine()->getRepository('AppBundle:Media')->findBy(array(), array('created' => 'DESC')); //date
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

    /**
     * @Route("/ajax/vote", name="medias_ajax_vote")
     */
    public function voteAction(Request $request)
    {
        if($request->isXMLHttpRequest()){
          // on récupère le média
          $media = $this->getDoctrine()->getRepository('AppBundle:Media')->findOneById($request->get('idMedia'));
          $mediaService = $this->get('app.media_service');

          // on check si il n'a pas déjà voté
          if($mediaService->check($media)){
            return new Response('Vous avez déjà voté.', 400);
          }

          // on créer le vote
          $vote = new Vote();
          $vote->setMedia($media);
          $vote->setUtilisateur($this->getUser());

          // on update la session
          $session = $this->get('session');
          $votes = $session->get('votes');
          $votes[] = $vote;
          $session->set('votes', $votes);

          // on le push en bdd
          $em = $this->getDoctrine()->getManager();
          $em->persist($vote);
          $em->flush();

          return new Response('ok', 200);
        }
        return new Response('pok', 400);
    }

    /**
     * @Route("/ajax/devote", name="medias_ajax_devote")
     */
    public function devoteAction(Request $request)
    {
        if($request->isXMLHttpRequest()){
          // on récupère le média
          $media = $this->getDoctrine()->getRepository('AppBundle:Media')->findOneById($request->get('idMedia'));
          $mediaService = $this->get('app.media_service');

          if($vote = $mediaService->check($media)){

            // on update la session
            $session = $this->get('session');
            $votes = $session->get('votes');
            unset($votes[array_search($vote, $votes)]);
            $session->set('votes', $votes);

            // on le push en bdd
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($this->getDoctrine()->getRepository('AppBundle:Vote')->findOneById($vote->getId()));
            $em->flush();

            return new Response('ok', 200);
          }
        }
        return new Response('pok', 400);
    }
}
