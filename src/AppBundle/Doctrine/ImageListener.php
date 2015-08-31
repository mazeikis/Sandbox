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
        }

    }


    private function handleEvent(Image $image)
    {
        $image->setCreated(new \Datetime());
        $image->setUpdated(new \Datetime());

    }


}
