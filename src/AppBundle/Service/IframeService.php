<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

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
      $uri = $request->headers->get('referer');

      /*
      $baseUrl = $request->getBaseUrl();
      $lastPath = substr($uri, strpos($uri, $baseUrl) + strlen($baseUrl));
      $routes = $this->router->getRouteCollection()->all();
      $route = $this->router->match($lastPath);//['_route'];
      */

      return $uri ? true : false;
    } catch (\Exception $e) {

    }
    return true;
  }
}
