<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CronControllerTest extends WebTestCase
{
  public function testCronFile()
  {
      $client = self::createClient();
      $url = $client->getContainer()->get('router')->generate('cron_file');
      $client->request('GET', $url);
  }
}
