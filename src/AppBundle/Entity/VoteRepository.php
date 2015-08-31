<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\Image;

/**
 * VoteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VoteRepository extends EntityRepository
{
	public function checkForVote(User $user, Image $image)
	{
		$vote = $this->findOneBy(array('user' => $user, 'image' => $image));
		if ($vote !== null) {
			return $vote;
		} else {
			return false;
		}

	}


	public function countVotes(Image $image)
	{
		$query = $this->createQueryBuilder('vote');
		$query->select('SUM(vote.vote) as votes_sum')
			  ->where('vote.image = :image')
			  ->setParameter('image', $image);
		return $query;

	}

}
