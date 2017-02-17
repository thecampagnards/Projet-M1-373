<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Utilisateur;

/**
 * CameraRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CameraRepository extends \Doctrine\ORM\EntityRepository
{
  /**
   * @param Utilisateur $utilisateur
   *
   * @return array
   */
    public function findByUtilisateur(Utilisateur $utilisateur)
    {
      dump('ok');
      $qb = $this->_em->createQueryBuilder();
      $qb->select('u')
          ->from($this->_entityName, 'u')
          ->leftJoin('u.utilisateurs','us')
          ->where('u.etat = 1')
          ->orWhere('us.id = :utilisateur_id')
          ->setParameter('utilisateur_id', $utilisateur->getId());

      return $qb->getQuery()->getResult();
    }
}
