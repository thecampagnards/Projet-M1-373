<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Camera
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="camera")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CameraRepository")
 */
class Camera extends BaseFile
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=65)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=20, nullable=true, unique=true)
     */
    private $ip;

    /**
     * @var bool
     *
     * @ORM\Column(name="etat", type="boolean")
     */
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="emailPassword", type="string", length=255, nullable=true)
     */
    private $emailPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="FTPUser", type="string", length=255, nullable=true, unique=true)
     */
    private $FTPUser;

    /**
     * @var string
     *
     * @ORM\Column(name="FTPassword", type="string", length=255, nullable=true)
     */
    private $FTPPassword;

    /**
     * @var int
     *
     * @ORM\Column(name="viewer", type="integer", nullable=true)
     */
    private $viewer;

    /**
     *  @ORM\OneToMany(targetEntity="Media", mappedBy="camera")
     */
    private $medias;

    /**
     * @ORM\ManyToMany(targetEntity="Utilisateur", inversedBy="cameras")
     * @ORM\JoinTable(name="utilisateurs_cameras")
     */
    private $utilisateurs;

    protected function getUploadDir()
    {
        return 'uploads/camera/'.$this->getId().'/';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->medias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->utilisateurs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
      return $this->nom;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Camera
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Camera
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Camera
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set etat
     *
     * @param boolean $etat
     *
     * @return Camera
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return boolean
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Camera
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set viewer
     *
     * @param integer $viewer
     *
     * @return Camera
     */
    public function setViewer($viewer)
    {
        $this->viewer = $viewer;

        return $this;
    }

    /**
     * Get viewer
     *
     * @return integer
     */
    public function getViewer()
    {
        return $this->viewer;
    }

    /**
     * Add media
     *
     * @param \AppBundle\Entity\Media $media
     *
     * @return Camera
     */
    public function addMedia(\AppBundle\Entity\Media $media)
    {
        $this->medias[] = $media;

        return $this;
    }

    /**
     * Remove media
     *
     * @param \AppBundle\Entity\Media $media
     */
    public function removeMedia(\AppBundle\Entity\Media $media)
    {
        $this->medias->removeElement($media);
    }

    /**
     * Get medias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * Add utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     *
     * @return Camera
     */
    public function addUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateurs[] = $utilisateur;

        return $this;
    }

    /**
     * Remove utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     */
    public function removeUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateurs->removeElement($utilisateur);
    }

    /**
     * Get utilisateurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtilisateurs()
    {
        return $this->utilisateurs;
    }

    /**
     * Set emailPassword
     *
     * @param string $emailPassword
     *
     * @return Camera
     */
    public function setEmailPassword($emailPassword)
    {
        $this->emailPassword = $emailPassword;

        return $this;
    }

    /**
     * Get emailPassword
     *
     * @return string
     */
    public function getEmailPassword()
    {
        return $this->emailPassword;
    }

    /**
     * Set fTPUser
     *
     * @param string $fTPUser
     *
     * @return Camera
     */
    public function setFTPUser($fTPUser)
    {
        $this->FTPUser = $fTPUser;

        return $this;
    }

    /**
     * Get fTPUser
     *
     * @return string
     */
    public function getFTPUser()
    {
        return $this->FTPUser;
    }

    /**
     * Set fTPPassword
     *
     * @param string $fTPPassword
     *
     * @return Camera
     */
    public function setFTPPassword($fTPPassword)
    {
        $this->FTPPassword = $fTPPassword;

        return $this;
    }

    /**
     * Get fTPPassword
     *
     * @return string
     */
    public function getFTPPassword()
    {
        return $this->FTPPassword;
    }
}
