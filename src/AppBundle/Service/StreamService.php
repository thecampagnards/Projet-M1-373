<?php

namespace AppBundle\Service;

use AppBundle\Entity\Camera;

class StreamService
{
  public function getHttpResponseCode($url)
  {
    try {
      return substr(get_headers($url)[0], 9, 3);
    } catch (\Exception $e) {
      return false;
    }
  }

  public function streamOnline($url)
  {
    ($this->getHttpResponseCode($url) == 200) ? true : false;
  }
}
