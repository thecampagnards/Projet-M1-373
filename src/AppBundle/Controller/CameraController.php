<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Camera;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
      }elseif($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
        $cameras = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:Camera')->findByUtilisateur($this->getUser());
      }else{
        $cameras = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:Camera')->findByEtat(true);
      }
        return $this->render('pages/cameras.html.twig', array('cameras' => $cameras));
    }

    /**
     * @Route("/camera/{id}", name="camera")
     * @ParamConverter("camera", class="AppBundle:Camera")
     */
    public function cameraAction(Camera $camera)
    {
        // si pas admin
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $camera->getEtat() === false){
          // check privee + connexion
          if($currentUtilisateur = $this->getUser()){
            foreach ($camera->getUtilisateurs() as $utilisateur) {
              if($utilisateur->getId() === $currentUtilisateur->getId()){
                // check si dedans
                $currentUtilisateur = null;
                break;
              }
            }
            if(!empty($currentUtilisateur)){
              // 404
              throw $this->createNotFoundException('La caméra n\'existe pas.');
            }
          }else {
            // 404
            throw $this->createNotFoundException('La caméra n\'existe pas.');
          }
        }
        return $this->render('pages/camera.html.twig', array('camera' => $camera));
    }

    /**
     * @Route("/ajax/spectateur", name="cameras_ajax_spectateur")
     */
    public function spectateurAction(Request $request)
    {
        if($request->isXMLHttpRequest()){
          // on récupère la camera
          $camera = $this->getDoctrine()->getRepository('AppBundle:Camera')->findOneById($request->get('idCamera'));
          $camera->setViewer($camera->getViewer() + 1);
          // on le push en bdd
          $em = $this->getDoctrine()->getManager();
          $em->persist($camera);
          $em->flush();

          return new Response('ok', 200);
        }
        return new Response('pok', 400);
    }

    /**
     * @Route("/ajax/despectateur", name="cameras_ajax_despectateur")
     */
    public function despectateurAction(Request $request)
    {
        if($request->isXMLHttpRequest()){
          // on récupère la camera
          $camera = $this->getDoctrine()->getRepository('AppBundle:Camera')->findOneById($request->get('idCamera'));
          $camera->setViewer($camera->getViewer() - 1);
          // on le push en bdd
          $em = $this->getDoctrine()->getManager();
          $em->persist($camera);
          $em->flush();

          return new Response('ok', 200);
        }
        return new Response('pok', 400);
    }

    /**
     * @Route("/cameras/reset", name="cameras_reset")
     */
    public function resetAction()
    {
        // on récupère les cameras
        $cameras = $this->getDoctrine()->getRepository('AppBundle:Camera')->findAll();
        $em = $this->getDoctrine()->getManager();
        foreach ($cameras as $camera) {
          // on met les viewers à 0
          $camera->setViewer(0);
          // on le push en bdd
          $em->persist($camera);
        }
        $em->flush();
        return new Response('ok', 200);
    }
}
