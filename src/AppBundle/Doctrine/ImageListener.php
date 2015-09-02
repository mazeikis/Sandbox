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
            $this->handleEvent($entity);
            $entity->setCreated(new \DateTime());
        }

    }


    private function preUpdate(Image $image)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Image) {
            $this->handleEvent($entity);
            $entity->setUpdated(new \DateTime());
        }

    }


}
