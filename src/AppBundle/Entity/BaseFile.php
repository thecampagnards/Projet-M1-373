<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class BaseFile
{
  /**
   * @var string
   *
   * @ORM\Column(name="media", type="string", length=255, nullable=true)
   */
  protected $media;

  /**
   * @Assert\File(maxSize="250000000")
   */
  protected $file;

  public function getAbsolutePath()
  {
    return null === $this->media ? null : $this->getUploadRootDir().'/'.$this->media;
  }

  public function getWebPath()
  {
    return null === $this->media ? null : $this->getUploadDir().'/'.$this->media;
  }

  protected function getUploadRootDir()
  {
    return __DIR__.'/../../../web/'.$this->getUploadDir();
  }

  /**
  * @ORM\PrePersist()
  * @ORM\PreUpdate()
  */
  public function preUploadFile() {
    if (null !== $this->getFile()) {
      $filename = $this->generateRandomFilename();
      $this->setMedia($filename.'.'.$this->getFile()->guessExtension());
    }
  }

  /**
  * Generates a 32 char long random filename
  *
  * @return string
  */
  public function generateRandomFilename() {
    $count = 0;
    do {
      $random = random_bytes(16);
      $randomString = bin2hex($random);
      $count++;
    }
    while(file_exists($this->getUploadRootDir().'/'.$randomString.'.'.$this->getFile()->guessExtension()) && $count < 50);

    return $randomString;
  }

  /**
  * @ORM\PostPersist()
  * @ORM\PostUpdate()
  *
  * @return mixed
  */
  public function uploadFile() {
    if($this->getFile() !== null){
      $this->getFile()->move($this->getUploadRootDir(), $this->getMedia());
      $this->setFile(null);
    }
  }

  /**
  * @ORM\PostRemove()
  */
  public function removeFile()
  {
    if ($file = $this->getAbsolutePath() && file_exists($this->getAbsolutePath())) {
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
    if($this->getAbsolutePath() &&
      file_exists($this->getAbsolutePath()) &&
      is_array(getimagesize($this->getAbsolutePath()))
    ){
      return true;
    }
    return false;
  }

  /**
  * Check if file is video
  *
  * @return boolean
  */
  public function isVideo()
  {
    if($this->getAbsolutePath() &&
      file_exists($this->getAbsolutePath()) &&
      strstr(mime_content_type($this->getAbsolutePath()), 'video/')
    ){
      return true;
    }
    return false;
  }

  /**
   * Get media
   *
   * @return string
   */
  public function getMedia()
  {
      return $this->media;
  }

  /**
   * Set media
   *
   * @param string $media
   *
   * @return Projet
   */
  public function setMedia($media)
  {
      $this->media = $media;

      return $this;
  }

  /**
   * Get file.
   *
   * @return UploadedFile
   */
  public function getFile()
  {
      return $this->file;
  }

  /**
   * Sets file.
   *
   * @param UploadedFile $file
   */
  public function setFile(UploadedFile $file = null)
  {
      $this->file = $file;
  }
}
