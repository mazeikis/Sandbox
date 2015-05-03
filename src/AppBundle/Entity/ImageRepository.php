<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;


class ImageRepository extends EntityRepository
{
    public function SearchForQuery($q, $limit = null, $offset = null){
        $query = $this->createQueryBuilder('image');
            $query->select('image')
                  ->leftJoin('image.owner', 'users', Join::WITH)
                  ->where($query->expr()->orX(
                      $query->expr()->like('image.title', ':key'),
                      $query->expr()->like('image.description', ':key'),
                      $query->expr()->like('users.username', ':key')
                      ))
                  ->orderBy('image.created', 'DESC')
                  ->setParameter('key', '%'.$q.'%')
                  ->setMaxResults($limit)
                  ->setFirstResult($offset);
            $result = $query->getQuery()->getResult();

        return $result;
    }
    public function GetRecentlyUploaded($count, $slug = null)
    {
        if ($slug != null){
            return $this->findBy(array('owner' => $slug), array('created' => 'DESC'), $count);

        }else {
            return $this->findBy(array(), array('created' => 'DESC'), $count);
        }
    }
}