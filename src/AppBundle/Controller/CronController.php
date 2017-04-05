<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Media;

class CronController extends Controller
{
  /**
   * @Route("/cron/file", name="cron_file")
   */
  public function indexFile(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    echo '--------<br/>';
    foreach (glob($this->get('kernel')->getRootDir() . '/../web/uploads/camera/*/*/*.*') as $filename) {

      // on recupère les infos
      $result = explode('/', $filename);
      $size = count($result);
      $file = $result[$size - 1];
      $etat = $result[$size - 2];
      $camera = $result[$size - 3];

      // si il y a un media
      if($media = $this->getDoctrine()->getRepository('AppBundle:Media')->findOneBy(array('media' => $file))){
        // si il y a des modifs
        if($media->getCamera()->getId() != $camera || $media->getEtat() != ($etat === 'public' ? true : false)){
          if($camera = $this->getDoctrine()->getRepository('AppBundle:Camera')->findOneById($camera)){

            $media->setCamera($camera);
            $media->setEtat(($etat === 'public' ? true : false));

            // on le push en bdd
            $em->persist($media);

            echo $media->getNom() . ' modifié !<br/>';
          }
        }
      }
      // si il n'y a pas de media
      else{
        // on récupère la caméra
        if($camera = $this->getDoctrine()->getRepository('AppBundle:Camera')->findOneById($camera)){

          // on creer le media
          $media = new Media();
          $media->setNom($file);
          $media->setMedia($file);
          $media->setCamera($camera);
          $media->setEtat(($etat === 'public' ? true : false));

          // on le push en bdd
          $em->persist($media);

          echo $media->getNom() . ' ajouté !<br/>';
        }

      }
    }
    echo '--------<br/>';
    $em->flush();

    $response = new Response();
    $response->setStatusCode(Response::HTTP_OK);
    return $response;
  }
}
