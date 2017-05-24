<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use AppBundle\Entity\Camera;

class IframeService
{
  protected $requestStack, $router;

  public function __construct(RequestStack $requestStack, Router $router)
  {
    $this->requestStack = $requestStack;
    $this->router = $router;
  }

  public function isIframe()
  {
    try {
      $request = $this->requestStack->getCurrentRequest();

      /*
      $uri = $request->headers->get('referer');
      $baseUrl = $request->getBaseUrl();
      $lastPath = substr($uri, strpos($uri, $baseUrl) + strlen($baseUrl));
      $routes = $this->router->getRouteCollection()->all();
      $route = $this->router->match($lastPath);//['_route'];
      */

      if(empty($request->headers->get('referer'))){
        return false;
      }

      return strpos($request->headers->get('referer'), $request->getHost()) ? false : true;

    } catch (\Exception $e) {

    }
    return true;
  }

  public function isIframeAccess(Camera $camera)
  {
  if($this->isIframe() && $camera->getEtat()){
      $request = $this->requestStack->getCurrentRequest();
      $utilisateurs = $camera->getUtilisateurs();
      foreach ($utilisateurs as $utilisateur) {
        if($utilisateur->getIpNdd()->contains($request->getHost())){
          return true;
        }
      }
      return false;
    }
    return true;
  }
}
