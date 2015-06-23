<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class ImageRepository extends EntityRepository
{


    public function searchForQuery($q, $sortBy, $order)
    {
      if ($sortBy === 'rating') {
        $sortBy = 'votes_sum';
      } else {
        $sortBy = 'image.'.$sortBy;
      }
      $query = $this->getEntityManager()->createQuery(
           'SELECT image,
            SUM(votes.vote) as votes_sum
            FROM AppBundle\Entity\Image image
            LEFT JOIN image.owner user
            LEFT JOIN image.votes votes
            WHERE image.title LIKE :key
            OR image.description LIKE :key 
            OR user.username LIKE :key  
            GROUP BY image.id 
            ORDER BY '.$sortBy.' '.$order);
      $query->setParameter('key', '%'.$q.'%');
      return $query;

    }

    //end searchForQuery()


    public function getImagesQuery($sortBy, $order)
    {
        if ($sortBy === 'rating') {
          $sortBy = 'votes_sum';
        } else {
          $sortBy = 'image.'.$sortBy;
        }
        $query = $this->getEntityManager()->createQuery(
            'SELECT image, 
             SUM(votes.vote) as votes_sum
             FROM AppBundle\Entity\Image image 
             LEFT JOIN image.votes votes 
             GROUP BY image.id 
             ORDER BY '.$sortBy.' '.$order
            );
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
