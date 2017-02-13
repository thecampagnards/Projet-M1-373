<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Camera
 *
 * @ORM\Table(name="camera")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CameraRepository")
 */
class Camera
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
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

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

    /**
    * @ORM\OneToOne(targetEntity="AppBundle\Entity\Fichier", cascade={"persist"})
    */
    private $image;
}
