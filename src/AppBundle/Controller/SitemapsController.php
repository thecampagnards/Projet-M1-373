<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SitemapsController extends Controller
{

    /**
     * @Route("/sitemap.{_format}", name="sample_sitemaps_sitemap", Requirements={"_format" = "xml"})
     */
    public function sitemapAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $urls = array();
        $hostname = $this->getRequest()->getHost();

        // add some urls homepage
        $urls[] = array('loc' => $this->get('router')->generate('homepage'), 'changefreq' => 'weekly', 'priority' => '1.0');

        // multi-lang pages
        foreach($languages as $lang) {
            $urls[] = array('loc' => $this->get('router')->generate('contact', array('_locale' => $lang)), 'changefreq' => 'monthly', 'priority' => '0.3');
        }

        // urls from database
        $urls[] = array('loc' => $this->get('router')->generate('cameras', array('_locale' => $lang)), 'changefreq' => 'weekly', 'priority' => '0.7');
        // service
        foreach ($em->getRepository('AppBundle:Media')->findAll() as $product) {
            $urls[] = array('loc' => $this->get('router')->generate('media',
                    array('id' => $media->getId())), 'priority' => '0.5');
        }

        return $this->render('pages/sitemap.xml.twig', array('urls' => $urls, 'hostname' => $hostname));
    }
}
