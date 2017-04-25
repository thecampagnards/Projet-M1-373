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
  public function fileAction(Request $request)
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

  /**
   * @Route("/cron/mail", name="cron_mail")
   */
  public function mailAction(Request $request){

    echo '--------<br/>';

    $cameras = $this->getDoctrine()->getRepository('AppBundle:Camera')->findAll();

    foreach ($cameras as $camera) {

    // connexion au serveur
    $inbox = imap_open(
      '{'.$this->getParameter('mailer_inbox_host').':'.$this->getParameter('mailer_inbox_port').'/imap/ssl}INBOX',
      $camera->getEmail(),
      $camera->getEmailPassword(),
      NULL,
      1
    ) or die('Impossible de se connecter au serveur mail : ' . print_r(imap_errors()));

       /* ALL - return all messages matching the rest of the criteria
        ANSWERED - match messages with the \\ANSWERED flag set
        BCC "string" - match messages with "string" in the Bcc: field
        BEFORE "date" - match messages with Date: before "date"
        BODY "string" - match messages with "string" in the body of the message
        CC "string" - match messages with "string" in the Cc: field
        DELETED - match deleted messages
        FLAGGED - match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
        FROM "string" - match messages with "string" in the From: field
        KEYWORD "string" - match messages with "string" as a keyword
        NEW - match new messages
        OLD - match old messages
        ON "date" - match messages with Date: matching "date"
        RECENT - match messages with the \\RECENT flag set
        SEEN - match messages that have been read (the \\SEEN flag is set)
        SINCE "date" - match messages with Date: after "date"
        SUBJECT "string" - match messages with "string" in the Subject:
        TEXT "string" - match messages with text "string"
        TO "string" - match messages with "string" in the To:
        UNANSWERED - match messages that have not been answered
        UNDELETED - match messages that are not deleted
        UNFLAGGED - match messages that are not flagged
        UNKEYWORD "string" - match messages that do not have the keyword "string"
        UNSEEN - match messages which have not been read yet*/

    // récupération des mails non lus
    $emails = imap_search($inbox,'UNSEEN');

    if($emails){
      // on parcours les mails
      foreach($emails as $mail) {

          // récupération des informations en entete du mail
          $headerInfo = imap_headerinfo($inbox,$mail);

          // récupération de la structure du mail
          $structure = imap_fetchstructure($inbox,$mail);

          $attachments = array();
          if(isset($structure->parts) && count($structure->parts)) {

            for($i = 0; $i < count($structure->parts); $i++) {

              // récupération des infos du fichier
              $attachments[$i] = array(
                'is_attachment' => false,
                'filename' => '',
                'name' => '',
                'attachment' => ''
              );

              if($structure->parts[$i]->ifdparameters) {
                foreach($structure->parts[$i]->dparameters as $object) {
                  if(strtolower($object->attribute) == 'filename') {
                    $attachments[$i]['is_attachment'] = true;
                    $attachments[$i]['filename'] = $object->value;
                  }
                }
              }

              if($structure->parts[$i]->ifparameters) {
                foreach($structure->parts[$i]->parameters as $object) {
                  if(strtolower($object->attribute) == 'name') {
                    $attachments[$i]['is_attachment'] = true;
                    $attachments[$i]['name'] = $object->value;
                  }
                }
              }

              // récupération du fichier
              if($attachments[$i]['is_attachment']) {
                $attachments[$i]['attachment'] = imap_fetchbody($inbox, $mail, $i+1);
                if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                  $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                  file_put_contents($this->get('kernel')->getRootDir() . '/../web/uploads/camera/'. $camera->getId() .'/'. ($camera->getEtat() ? 'public' : 'prive') .'/' . $attachments[$i]['name'], $attachments[$i]['attachment']);
                  echo $attachments[$i]['name'] . ' ajouté !<br/>';
                }
                elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                  $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                }
              }
            }
          }
      }
    }
  }

    // fermeture de la connexion au serveur mail
    imap_expunge($inbox);
    imap_close($inbox);

    echo '--------<br/>';

    $response = new Response();
    $response->setStatusCode(Response::HTTP_OK);
    return $response;
  }
}
