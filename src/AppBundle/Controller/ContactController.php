<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\ContactType;
use AppBundle\Entity\Contact;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="contact")
     */
    public function indexAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
          if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            //creation du mail
            $adminMails = $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('AppBundle:Utilisateur')->findEmailsByRole('ADMIN');

            if($form->get('copie')->getData()){
              dump('ok');
              $adminMails[] = $contact->getEmail();
            }

            $message = \Swift_Message::newInstance()
              ->setSubject('Contact depuis votre site')
              ->setFrom($this->getParameter('mailer_user'))
              ->setBcc($adminMails)
              ->setBody(
                $this->renderView(
                  'emails/contact.html.twig',
                    array('contact' => $contact)
                  ),
                'text/html'
              );
            //envoi du mail
            $this->get('mailer')->send($message);
          }
        }
        return $this->render('pages/contact.html.twig', array('form' => $form->createView()));
    }
}
