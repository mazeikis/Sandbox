<?php
namespace AppBundle\Doctrine;

use AppBundle\Entity\Image;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ImageListener
{


    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Image) {
            $entity->setCreated(new \DateTime());
        }

    }


    private function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Image) {
            $entity->setUpdated(new \DateTime());
        }

    }


}
