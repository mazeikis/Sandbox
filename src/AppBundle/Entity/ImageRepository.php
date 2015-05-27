<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class ImageRepository extends EntityRepository
{


    public function searchForQuery($q, $sortBy, $order)
    {
        $query = $this->createQueryBuilder('image');
        $query->select('image')
              ->leftJoin('image.owner', 'users', Join::WITH)
              ->where(
                  $query->expr()->orX(
                      $query->expr()->like('image.title', ':key'),
                      $query->expr()->like('image.description', ':key'),
                      $query->expr()->like('users.username', ':key')
                  )
              )
              ->orderBy('image.'.$sortBy, $order)
              ->setParameter('key', '%'.$q.'%');
        return $query;

    }//end searchForQuery()


    public function getRecentlyUploaded($count, $slug=null)
    {
        if ($slug !== null) {
            return $this->findBy(array('owner' => $slug), array('created' => 'DESC'), $count);
        } else {
            return $this->findBy(array(), array('created' => 'DESC'), $count);
        }

    }//end getRecentlyUploaded()


}//end class
