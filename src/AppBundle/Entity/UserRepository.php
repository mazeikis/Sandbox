<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
	public function test(){
		return 'TEST METHOD, PLEASE IGNORE';
	}
}