<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{


    public function getImages($sortBy, $order, $q = null)
    {
      if ($sortBy === 'rating') {
        $sortBy = 'votes_sum';
      } else {
        $sortBy = 'image.'.$sortBy;
      }
      $query = $this->createQueryBuilder('image');
      $query->select('image')->addSelect('SUM(votes.vote) as votes_sum')
            ->leftJoin('image.votes', 'votes');

      //block for search keyword only
      if($q !== null){
            $query->leftJoin('image.owner', 'user')
                  ->where(
                          $query->expr()->orX(
                                              $query->expr()->like('image.title', ':key'),
                                              $query->expr()->like('image.description', ':key'),
                                              $query->expr()->like('user.username', ':key')
                                              )
                          )
            ->setParameter('key', '%'.$q.'%');
           }//end of search keyword block
  
      $query->groupBy('image.id')->orderBy($sortBy, $order);

      return $query;

    }//end getImages()


}//end class
