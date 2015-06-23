<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{


    public function searchForQuery($q, $sortBy, $order)
    {
      if ($sortBy === 'rating') {
        $sortBy = 'votes_sum';
      } else {
        $sortBy = 'image.'.$sortBy;
      }
      $query = $this->createQueryBuilder('image');
      $query->select('image')->addSelect('SUM(votes.vote) as votes_sum')
            ->leftJoin('image.owner', 'user')
            ->leftJoin('image.votes', 'votes')
            ->where(
                $query->expr()->orX(
                  $query->expr()->like('image.title', ':key'),
                  $query->expr()->like('image.description', ':key'),
                  $query->expr()->like('user.username', ':key')
                  )
                )
            ->groupBy('image.id')
            ->orderBy($sortBy, $order);    
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
        $query = $this->createQueryBuilder('image');
        $query->select('image')->addSelect('SUM(votes.vote) AS votes_sum')
              ->leftJoin('image.votes', 'votes')
              ->groupBy('image.id')
              ->orderBy($sortBy, $order);
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
