<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Media
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaRepository")
 */
class Media extends BaseFile
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

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
     *  @ORM\OneToMany(targetEntity="Vote", mappedBy="media")
     */
    private $votes;

    /**
     * @ORM\ManyToOne(targetEntity="Camera", inversedBy="medias")
     * @ORM\JoinColumn(name="camera_id", referencedColumnName="id")
     */
    private $camera;

    /**
     * @var datetime $created
     *
     * @ORM\Column(type="datetime")
     */
     private $created;

     private $votesCount;
     public function getVotesCount(){
        return count($this->getVotes());
    }

    public function __construct()
    {
        $this->setCreated(new \DateTime("now"));
    }

    protected function getUploadDir()
    {
        return 'uploads/camera/'.$this->getCamera()->getId().'/'. ($this->getEtat() ? 'public/' : 'prive/');
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
     * @return Media
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
     * @return Media
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
     * Set etat
     *
     * @param boolean $etat
     *
     * @return Media
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
     * Set vote
     *
     * @param integer $vote
     *
     * @return Media
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return integer
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set camera
     *
     * @param \AppBundle\Entity\Camera $camera
     *
     * @return Media
     */
    public function setCamera(\AppBundle\Entity\Camera $camera = null)
    {
        $this->camera = $camera;

        return $this;
    }

    /**
     * Get camera
     *
     * @return \AppBundle\Entity\Camera
     */
    public function getCamera()
    {
        return $this->camera;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Media
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add vote
     *
     * @param \AppBundle\Entity\Vote $vote
     *
     * @return Media
     */
    public function addVote(\AppBundle\Entity\Vote $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \AppBundle\Entity\Vote $vote
     */
    public function removeVote(\AppBundle\Entity\Vote $vote)
    {
        $this->votes->removeElement($vote);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }
}
