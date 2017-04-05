<?php

namespace AppBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface as TokenStorage;
use Symfony\Bridge\Doctrine\RegistryInterface as Registry;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Entity\Media;

class MediaService
{
  private $session, $doctrine, $tokenStorage;

  public function __construct(Session $session, Registry $doctrine, TokenStorage $tokenStorage)
  {
    $this->session = $session;
    $this->doctrine = $doctrine;
    $this->tokenStorage = $tokenStorage;
  }

  public function checkSession(Media $media)
  {
    $votes = $this->session->get('votes');
    if(!empty($votes)){
      foreach ($votes as $key => $vote) {
        if($vote->getMedia()->getId() === $media->getId()){
          return $vote;
        }
      }
    }
    return null;
  }

  public function checkUtilisateur(Media $media)
  {
    return $this->doctrine->getRepository('AppBundle:Vote')->findOneBy(array('media' => $media, 'utilisateur' => $this->tokenStorage->getToken()->getUser()));
  }

  public function check(Media $media)
  {
    return $this->checkUtilisateur($media) ?? $this->checkSession($media);
  }

}
