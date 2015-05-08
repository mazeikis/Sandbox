<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class ImageRepository extends EntityRepository
{
    public function searchForQuery($q, $limit = null, $offset = null, $sortBy, $order){
        $query = $this->createQueryBuilder('image');
        $query->select('image')
              ->leftJoin('image.owner', 'users', Join::WITH)
              ->where($query->expr()->orX(
                  $query->expr()->like('image.title', ':key'),
                  $query->expr()->like('image.description', ':key'),
                  $query->expr()->like('users.username', ':key')
                  ))
              ->orderBy('image.'.$sortBy, $order)
              ->setParameter('key', '%'.$q.'%')
              ->setFirstResult($offset)
              ->setMaxResults($limit);
            $result = $query->getQuery()->getResult();
        return $result;
    }
    public function countResultRows($q)
    {
      $query = $this->getEntityManager()->createQuery(
          'SELECT COUNT(image)
          FROM AppBundle:Image image 
          LEFT JOIN image.owner user
          WHERE image.title LIKE :key
          OR image.description LIKE :key 
          OR user.username LIKE :key'
          )->setParameter('key', '%'.$q.'%');
      $result = $query->getSingleScalarResult();

        return $result;
    }
    public function getRecentlyUploaded($count, $slug = null)
    {
        if ($slug !== null){
            return $this->findBy(array('owner' => $slug), array('created' => 'DESC'), $count);

        }else {
            return $this->findBy(array(), array('created' => 'DESC'), $count);
        }
    }
}
