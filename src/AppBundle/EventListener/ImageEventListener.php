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
    public function onImageDeleteAction(ImageEvent $event){
        $fileSystem = new Filesystem();
        $imageDir   = $this->container->get('kernel')->getRootDir().'/../web/';
        $image      = $event->getImage();
        $fileSystem->remove($imageDir.$image->getPath());


        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $cacheManager->remove($image->getPath());
        $flash = $this->container->get('braincrafted_bootstrap.flash');
        $flash->alert('Image was successfully deleted.');

    }

    public function onImageCreateAction(ImageEvent $event){
        $image            = $event->getImage();
        $data             = $event->getData();

        $imageDir = $this->container->get('kernel')->getRootDir().'/../web/';
        $data['file']->move($imageDir.'images/', $image->getPath());
        $flash = $this->container->get('braincrafted_bootstrap.flash');
        $flash->success('Image sucessfully uploaded!');
    }

}
