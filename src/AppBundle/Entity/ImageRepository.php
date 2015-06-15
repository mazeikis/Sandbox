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


    public function getImagesQuery($sortBy, $order)
    {
        $query = $this->createQueryBuilder('image');
        $query->select('image')
              ->orderBy('image.'.$sortBy, $order);
        return $query;

    }//end getImagesQuery()


    public function getRecentlyUploaded($count, $slug = null)
    {
        $paramters = array();
        if ($slug !== null) {
            $paramters['owner'] = $slug;
        }
        return $this->findBy($paramters, array('created' => 'DESC'), $count);

    }//end getRecentlyUploaded()


}//end class
