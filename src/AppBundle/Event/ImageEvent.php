<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 18/09/2016
 * Time: 21:07
 */

namespace AppBundle\Event;


use AppBundle\Entity\Image;
use Symfony\Component\EventDispatcher\Event;

class ImageEvent extends Event
{
    const IMAGE_CREATE_EVENT = 'image.create.event';
    const IMAGE_DELETE_EVENT = 'image.delete.event';

    private $image;
    private $data;

    /**
     * ImageEvent constructor.
     */
    public function __construct(Image $image, $data = null)
    {
        $this->image = $image;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

}
