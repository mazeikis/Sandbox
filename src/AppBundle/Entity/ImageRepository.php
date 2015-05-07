<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
    public function searchForQuery($q, $limit = null, $offset = null, $sortBy, $order){
        $query = $this->getEntityManager()->createQuery(
          'SELECT image
          FROM AppBundle:Image image 
          LEFT JOIN image.owner user
          WHERE image.title LIKE :key
          OR image.description LIKE :key 
          OR user.username LIKE :key'
          )->setParameter('key', '%'.$q.'%')
          ->addOrderBy($sortBy, $order)
          ->setFirstResult($offset)
          ->setMaxResults($limit);
        $result = $query->getResult();

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
