<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="utilisateur")
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

    public function __construct()
    {
        parent::__construct();
        $this->ipNdd = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
