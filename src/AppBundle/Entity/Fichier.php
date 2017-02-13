<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity
* @ORM\HasLifecycleCallbacks()
*/
class Fichier
{

  /**
  * @ORM\Id
  * @ORM\Column(type="integer")
  * @ORM\GeneratedValue(strategy="AUTO")
  */
  protected $id;

  /**
   * @Assert\File(
   *     maxSize = "5048k",
   *     mimeTypesMessage = "Le fichier est trop lourd ({{ size }}), le maximum est de {{ limit }}"
   * )
   */
  private $fichier;

  private $tempPath;

  /**
  * @ORM\Column(type="string", length=255, nullable=true)
  */
  private $fichierChemin;

  /**
  * dossier par défaut
  * @ORM\Column(type="string", length=255, nullable=true)
  */
  private $fichierDossier = 'uploads/';

  /**
  * @param UploadedFile $file
  * @return Fichier
  */
  public function setFichier(UploadedFile $file = null) {
    $this->fichier = $file;
    if (isset($this->fichierChemin)) {
      $this->tempPath = $this->fichierChemin;
      $this->fichierChemin = null;
    } else {
      $this->fichierChemin = 'initial';
    }
    return $this;
  }

  /**
  * @return UploadedFile
  */
  public function getFichier() {

    return $this->fichier;
  }

  /**
  * @param string $fichierChemin
  * @return Fichier
  */
  public function setFichierChemin($fichierChemin)
  {
    $this->fichierChemin = $fichierChemin;

    return $this;
  }

  /**
  * Get fichierChemin
  *
  * @return string
  */
  public function getFilePath()
  {
    return $this->fichierChemin;
  }

  /**
  * Set fichierDossier
  *
  * @param string $fichierDossier
  * @return Fichier
  */
  public function setFichierDossier($fichierDossier)
  {
    $this->fichierDossier = $fichierDossier;

    return $this;
  }

  /**
  * Get fichierDossier
  *
  * @return string
  */
  public function getFichierDossier()
  {
    return $this->fichierDossier;
  }

  /**
  * Get the absolute path of the fichierChemin
  */
  public function getFichierAbsolutePath() {
    return null === $this->fichierChemin
    ? null
    : $this->getUploadRootDir().'/'.$this->fichierChemin;
  }

  /**
  * Get root directory for file uploads
  *
  * @return string
  */
  protected function getUploadRootDir($type='file') {

    return __DIR__.'/../../../web/'.$this->getFileFolder();
  }

  /**
  * Get the web path for the user
  *
  * @return string
  */
  public function getWebFilePath() {

    return '/'.$this->getFileFolder().'/'.$this->getFilePath();
  }

  /**
  * @ORM\PrePersist()
  * @ORM\PreUpdate()
  */
  public function preUploadFile() {
    //si le fichier n'est pas vide
    if (null !== $this->getfichier()) {
      $filename = $this->generateRandomfichiername();
      $this->setFilePath($filename.'.'.$this->getfichier()->guessExtension());
    }
  }

  /**
  * Generates a 32 char long random filename
  *
  * @return string
  */
  public function generateRandomfichiername() {
    $count = 0;
    do {
      $random = random_bytes(16);
      $randomString = bin2hex($random);
      $count++;
    }
    while(file_exists($this->getUploadRootDir().'/'.$randomString.'.'.$this->getfichier()->guessExtension()) && $count < 50);

    return $randomString;
  }

  /**
  * @ORM\PostPersist()
  * @ORM\PostUpdate()
  *
  * @return mixed
  */
  public function uploadFile() {
    if ($this->getfichier() === null) {
      return;
    }

    $this->getfichier()->move($this->getUploadRootDir(), $this->getFilePath());

    if (isset($this->tempPath) && file_exists($this->getUploadRootDir().'/'.$this->tempPath)) {
      unlink($this->getUploadRootDir().'/'.$this->tempPath);
      $this->tempPath = null;
    }
    $this->fichier = null;
  }

  /**
  * @ORM\PostRemove()
  */
  public function removefichier()
  {
    if ($file = $this->getFichierAbsolutePath() && file_exists($this->getFichierAbsolutePath())) {
      unlink($file);
    }
  }

  /**
  * Check if file is image
  *
  * @return boolean
  */
  public function isImage()
  {
    if($this->getFichierAbsolutePath() &&
      file_exists($this->getFichierAbsolutePath()) &&
      is_array(getimagesize($this->getFichierAbsolutePath()))
    ){
      return true;
    }
    return false;
  }
}
