<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaRepository")
 */
class Media
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
     * @ORM\Column(name="extension", type="string", length=10)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @var int
     *
     * @ORM\Column(name="vote", type="integer", nullable=true)
     */
    private $vote;

    /**
     * @ORM\ManyToOne(targetEntity="Camera", inversedBy="medias")
     * @ORM\JoinColumn(name="camera_id", referencedColumnName="id")
     */
    private $camera;

    /**
    * @ORM\OneToOne(targetEntity="AppBundle\Entity\Fichier", cascade={"persist"})
    */
    private $fichier;
}
