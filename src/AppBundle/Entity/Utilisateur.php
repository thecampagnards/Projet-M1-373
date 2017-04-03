<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UtilisateurRepository")
 */
class Utilisateur extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var array
     *
     * @ORM\Column(name="ip_ndd", type="array")
     */
    private $ipNdd;

    /**
     * @ORM\ManyToMany(targetEntity="Camera", mappedBy="utilisateurs")
     */
    private $cameras;

    /**
     *  @ORM\OneToMany(targetEntity="Vote", mappedBy="utilisateur")
     */
    private $votes;

    public function __construct()
    {
        parent::__construct();
        $this->ipNdd = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set ipNdd
     *
     * @param array $ipNdd
     *
     * @return Utilisateur
     */
    public function setIpNdd($ipNdd)
    {
        $this->ipNdd = $ipNdd;

        return $this;
    }

    /**
     * Get ipNdd
     *
     * @return array
     */
    public function getIpNdd()
    {
        return $this->ipNdd;
    }

    /**
     * Add camera
     *
     * @param \AppBundle\Entity\Camera $camera
     *
     * @return Utilisateur
     */
    public function addCamera(\AppBundle\Entity\Camera $camera)
    {
        $this->cameras[] = $camera;

        return $this;
    }

    /**
     * Remove camera
     *
     * @param \AppBundle\Entity\Camera $camera
     */
    public function removeCamera(\AppBundle\Entity\Camera $camera)
    {
        $this->cameras->removeElement($camera);
    }

    /**
     * Get cameras
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCameras()
    {
        return $this->cameras;
    }

    /**
     * Add vote
     *
     * @param \AppBundle\Entity\Vote $vote
     *
     * @return Utilisateur
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
