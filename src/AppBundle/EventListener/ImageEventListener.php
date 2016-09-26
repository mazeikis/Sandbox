<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 18/09/2016
 * Time: 21:11
 */

namespace AppBundle\EventListener;

use AppBundle\Event\ImageEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ImageEventListener
 * @package AppBundle\EventListener
 */
class ImageEventListener extends Event
{
    private $container;


    /**
     * ImageEventListener constructor.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param ImageEvent $event
     */
    public function onImageDeleteAction(ImageEvent $event) {
        $fileSystem = new Filesystem();
        $imageDir   = $this->container->getParameter('image_directory');
        $image      = $event->getImage();
        $fileSystem->remove($imageDir.$image->getFile());


        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $cacheManager->remove($image->getFile());
        $flash = $this->container->get('braincrafted_bootstrap.flash');
        $flash->alert('Image was successfully deleted.');

    }

    public function onImageCreateAction(ImageEvent $event) {
        $image = $event->getImage();
        $file  = $image->getFile() ;

        $imageSizeDetails = getimagesize($file->getPathname());
        $imageResolution  = strval($imageSizeDetails[0]).' x '.strval($imageSizeDetails[1]);
        $fileSize = $file->getSize();
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->container->getParameter('image_directory'), $fileName);

        $image->setFile($fileName);
        $image->setResolution($imageResolution)
              ->setCreated(new \DateTime())
              ->setUpdated(new \DateTime())
              ->setSize($fileSize);

        $flash = $this->container->get('braincrafted_bootstrap.flash');
        $flash->success('Image sucessfully uploaded!');
    }

}
